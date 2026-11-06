<?php

namespace App\Http\Controllers\Stock\StockItemDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Repositories\StockItemRepository;
use App\Models\StockItem;
use App\Models\StockItemPrice;
use App\Service\EmployeeService;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TabEditController extends Controller
{
    public function index(Request $request, StockItemRepository $stock_item_repo, $item_id, $tab_name)
    {
        $stock_item_info = $this->findById($item_id, $stock_item_repo);

        if ($stock_item_info->exists) {
            $stock_item_info->is_active = (int) $stock_item_info->is_active;
            $stock_item_info->is_sales_item = (int) $stock_item_info->is_sales_item;
        } else {
            $stock_item_info->pack_size = 1;
        }

        // Preload subcategory options so Select2 can bind the saved value on mount
        $subcategories = [];
        $parentGroupId = $stock_item_info->parent_item_group_id ?? null;
        if ($parentGroupId) {
            $subcategories = $this->fetchSubcategoryOptions($parentGroupId);
        }

        $details_edit_vue = [
            'item_id' => $item_id,
            'tab_name' => $tab_name,
            'stock_item_info' => $stock_item_info,
            'subcategories' => $subcategories,
            'urls' => [
                'button_cancel_url' => route('tab.stock.stock.item.list'),
                'form_save_url' => route('tab.stock.stock.item.save', ['item_id' => $item_id, 'tab_name' => $tab_name]),
            ],
            'dropdowns' => [
                'parent_categories' => \Facades\App\Models\StockItemGroup::parentStockItemGroupsDropdownJson(),
                'stock_brands' => \Facades\App\Models\StockBrand::stockBrandsDropdownJson(),
                'unit_of_measurements' => \Facades\App\Models\UnitOfMeasurement::unitOfMeasurementsDropdownJson(),
                'global_taxes' => \Facades\App\Models\GlobalTax::globalTaxesDropdownJson(),
            ],
        ];

        return view('stock.stock-item-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, StockItemRepository $stock_item_repo, EmployeeService $employeeService, $item_id, $tab_name)
    {
        if ($request->item_id != $item_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        $stock_item = $this->findById($item_id, $stock_item_repo);

        // Apply validation
        $this->applyValidation($request);

        // Editing an existing item requires the current user's password
        if ($item_id > 0) {
            $request->validate([
                'auth_password' => 'required|string',
            ], [
                'auth_password.required' => __('Password is required to update this stock item.'),
            ]);

            $passwordError = $employeeService->validateCurrentUserPassword($request->auth_password);
            if ($passwordError) {
                return response()->json([
                    'message' => $passwordError,
                    'js_notifications' => ['error' => $passwordError],
                    'errors' => ['auth_password' => [$passwordError]],
                ], 422);
            }
        }

        // Start database transaction
        $response = DB::transaction(function () use ($request, $stock_item) {
            return $this->saveFromRequest($request, $stock_item);
        });

        return response()->json([
            'gotourl' => route('tab.stock.stock.item.list'),
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request)
    {
        $rules = [
            'item_name' => 'required',
            'hsn_code' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9]+$/'],
            'stock_uom_id' => 'required',
            'pack_size' => 'required|numeric|gt:0',
            'parent_item_group_id' => 'nullable',
            'item_group_id' => 'nullable',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
        ];

        // Item code is required only for updates, optional for new items (will be auto-generated)
        if ($request->item_id > 0) {
            $rules['item_code'] = [
                'required',
                Rule::unique('stock_items', 'item_code')->ignore($request->item_id, 'item_id'),
            ];
        } elseif ($request->filled('item_code')) {
            $rules['item_code'] = [
                Rule::unique('stock_items', 'item_code'),
            ];
        }

        $request->validate($rules);
    }

    private function saveFromRequest(Request $request, StockItem $stock_item)
    {
        // Only set item_code if provided (for updates) or let model auto-generate it (for new items)
        if (! empty($request->item_code)) {
            $stock_item->item_code = $request->item_code;
        }
        $stock_item->item_name = $request->item_name;
        $stock_item->hsn_code = trim((string) $request->input('hsn_code', '')) ?: null;
        $stock_item->description = $request->description;
        $stock_item->parent_item_group_id = $request->parent_item_group_id ?: null;
        $stock_item->item_group_id = $request->item_group_id ?: null;
        $stock_item->brand_id = $request->brand_id ?: null;
        $stock_item->stock_uom_id = $request->stock_uom_id;
        $newPack = StockItem::normalizePackSize($request->pack_size);
        if ($stock_item->exists) {
            $oldPack = $stock_item->packSize();
            if (abs($oldPack - $newPack) > 0.0000001) {
                $stock_item->rescaleStockUnits($oldPack, $newPack);
            }
        }
        $stock_item->pack_size = $newPack;
        $stock_item->tax_percent = ($request->tax_percent === '' || $request->tax_percent === null)
            ? null
            : $request->tax_percent;
        $stock_item->is_active = (int) $request->is_active;
        $stock_item->is_sales_item = (int) $request->is_sales_item;
        $stock_item->cost_price = $request->cost_price ?? 0;
        $stock_item->selling_price = $request->selling_price ?? 0;

        $stock_item->save();

        // Inventory Item record
        InventoryItem::updateOrCreate([
            'item_id' => $stock_item->item_id,
        ], [
            'item_id' => $stock_item->item_id,
        ]);

        // Update prices without wiping quantity on edit (critical: quantity is stock on hand)
        $pricePayload = [
            'cost_price' => $request->cost_price ?? 0,
            'selling_price' => $request->selling_price ?? 0,
        ];

        $existingPrice = StockItemPrice::where('item_id', $stock_item->item_id)->first();
        if (! $existingPrice) {
            $pricePayload['quantity'] = 0;
        }

        StockItemPrice::updateOrCreate(
            ['item_id' => $stock_item->item_id],
            $pricePayload
        );

        voco_flash()->success(__('Stock item details updated!'));

        return $stock_item;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }

    public function getItemSubcategories(Request $request)
    {
        $parentId = $request->input('parent_id');

        return response()->json($this->fetchSubcategoryOptions($parentId));
    }

    private function fetchSubcategoryOptions($parentId): array
    {
        if (! $parentId) {
            return [];
        }

        return DB::table('stock_item_groups')
            ->select('group_name as text', 'item_group_id as id')
            ->where('parent_id', $parentId)
            ->where('is_active', 1)
            ->orderBy('group_name')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => (string) $row->id,
                    'text' => $row->text,
                ];
            })
            ->values()
            ->all();
    }
}
