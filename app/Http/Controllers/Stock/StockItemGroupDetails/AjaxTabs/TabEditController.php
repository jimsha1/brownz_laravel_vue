<?php

namespace App\Http\Controllers\Stock\StockItemGroupDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\StockItemGroup;
use App\Repositories\StockItemGroupRepository;
use App\Service\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabEditController extends Controller
{
    public function index(Request $request, StockItemGroupRepository $stock_item_group_repo, $item_group_id, $tab_name)
    {
        $stock_item_group_info = $this->findById($item_group_id, $stock_item_group_repo);

        if ($stock_item_group_info->exists) {
            $stock_item_group_info->is_active = (int) $stock_item_group_info->is_active;
            $stock_item_group_info->is_show_website = (int) $stock_item_group_info->is_show_website;
            if ($stock_item_group_info->parent_id !== null && $stock_item_group_info->parent_id !== '') {
                $stock_item_group_info->parent_id = (string) $stock_item_group_info->parent_id;
            }
        } else {
            $stock_item_group_info->is_active = 1;
            $stock_item_group_info->is_show_website = 1;
        }

        $parentOptions = collect(\Facades\App\Models\StockItemGroup::parentStockItemGroupsDropdownJson())
            ->map(function ($row) {
                return [
                    'id' => (string) $row->id,
                    'text' => $row->text,
                ];
            })
            ->when((int) $item_group_id > 0, function ($collection) use ($item_group_id) {
                return $collection->reject(function ($row) use ($item_group_id) {
                    return (string) $row['id'] === (string) $item_group_id;
                })->values();
            })
            ->all();

        $details_edit_vue = [
            'item_group_id' => $item_group_id,
            'tab_name' => $tab_name,
            'stock_item_group_info' => $stock_item_group_info,
            'urls' => [
                'button_cancel_url' => route('tab.stock.stock.item.group.list'),
                'form_save_url' => route('tab.stock.stock.item.group.save', [
                    'item_group_id' => $item_group_id,
                    'tab_name' => $tab_name,
                ]),
            ],
            'dropdowns' => [
                'parent_categories' => $parentOptions,
            ],
        ];

        return view('stock.stock-item-group-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, StockItemGroupRepository $stock_item_group_repo, EmployeeService $employeeService, $item_group_id, $tab_name)
    {
        if ($request->item_group_id != $item_group_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        $stock_item_group = $this->findById($item_group_id, $stock_item_group_repo);

        $this->applyValidation($request);

        if ((int) $item_group_id > 0) {
            $request->validate([
                'auth_password' => 'required|string',
            ], [
                'auth_password.required' => __('Password is required to update this stock item group.'),
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

        DB::transaction(function () use ($request, $stock_item_group) {
            return $this->saveFromRequest($request, $stock_item_group);
        });

        return response()->json([
            'gotourl' => route('tab.stock.stock.item.group.list'),
        ]);
    }

    private function applyValidation(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
            'parent_id' => 'nullable',
            'is_active' => 'nullable',
            'is_show_website' => 'nullable',
        ]);
    }

    private function saveFromRequest(Request $request, StockItemGroup $stock_item_group)
    {
        $stock_item_group->group_name = $request->group_name;
        $stock_item_group->parent_id = $request->filled('parent_id') ? $request->parent_id : null;
        $stock_item_group->is_show_website = (int) $request->is_show_website === 1 ? 1 : 0;
        $stock_item_group->is_active = (int) $request->is_active === 1 ? 1 : 0;
        $stock_item_group->save();

        voco_flash()->success(__('Stock item group details updated!'));

        return $stock_item_group;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }
}
