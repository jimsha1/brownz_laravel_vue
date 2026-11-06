<?php

namespace App\Http\Controllers\Master\ServiceDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceUsedProduct;
use App\Models\StockItem;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabUsedProductsController extends Controller
{
    public function index(Request $request, ServiceRepository $service_repo, $service_id, $tab_name)
    {
        $service = Service::findOrFail($service_id);

        $products = ServiceUsedProduct::with('item.stockUom')
            ->where('service_id', $service_id)
            ->orderBy('service_used_product_id')
            ->get()
            ->map(function (ServiceUsedProduct $row) {
                $item = $row->item;
                $pack = $item ? $item->packSize() : 1.0;
                $cost = $item ? (float) ($item->cost_price ?? 0) : 0;

                return [
                    'item_id' => (int) $row->item_id,
                    'item_name' => $item->item_name ?? ('Item #'.$row->item_id),
                    'item_code' => $item->item_code ?? '',
                    'uom_name' => optional(optional($item)->stockUom)->uom_name,
                    'quantity' => (float) $row->quantity,
                    'current_stock' => $item ? (float) $item->current_stock : 0,
                    'cost_price' => $cost,
                    'pack_size' => $pack,
                ];
            })
            ->values();

        $stockItems = StockItem::query()
            ->leftJoin('unit_of_measurements', 'unit_of_measurements.uom_id', '=', 'stock_items.stock_uom_id')
            ->where('stock_items.is_active', 1)
            ->where('stock_items.is_sales_item', 0)
            ->orderBy('stock_items.item_name')
            ->get([
                'stock_items.item_id',
                'stock_items.item_name',
                'stock_items.item_code',
                'stock_items.current_stock',
                'stock_items.cost_price',
                'stock_items.pack_size',
                'unit_of_measurements.uom_name',
            ])
            ->map(function ($item) {
                $pack = StockItem::normalizePackSize($item->pack_size ?? 1);

                return [
                    'item_id' => (int) $item->item_id,
                    'item_name' => $item->item_name,
                    'item_code' => $item->item_code,
                    'uom_name' => $item->uom_name,
                    'current_stock' => (float) $item->current_stock,
                    'cost_price' => (float) ($item->cost_price ?? 0),
                    'pack_size' => $pack,
                ];
            })
            ->values();

        $details_edit_vue = [
            'service_id' => (int) $service_id,
            'tab_name' => $tab_name,
            'service_name' => $service->name,
            'products' => $products,
            'stock_items' => $stockItems,
            'new_item_id' => '',
            'new_quantity' => 1,
            'urls' => [
                'button_cancel_url' => route('tab.master.service.details', [
                    'service_id' => $service_id,
                    'tab_name' => 'info',
                ]),
                'form_save_url' => route('tab.master.service.save', [
                    'service_id' => $service_id,
                    'tab_name' => $tab_name,
                ]),
            ],
        ];

        return view('master.service-details.ajax-tabs.tab-used-products', compact('details_edit_vue'));
    }

    public function save(Request $request, ServiceRepository $service_repo, $service_id, $tab_name)
    {
        if ((int) $request->service_id !== (int) $service_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        Service::findOrFail($service_id);

        $rows = $request->input('products', []);
        if (! is_array($rows)) {
            $rows = [];
        }

        $clean = [];
        $seen = [];
        foreach ($rows as $row) {
            $itemId = (int) ($row['item_id'] ?? 0);
            $quantity = round((float) ($row['quantity'] ?? 0), 3);
            if ($itemId <= 0 || $quantity <= 0) {
                continue;
            }
            if (isset($seen[$itemId])) {
                return response()->json([
                    'js_notifications' => ['error' => __('Each product can be added only once.')],
                ], 422);
            }
            $seen[$itemId] = true;
            $clean[] = [
                'item_id' => $itemId,
                'quantity' => $quantity,
            ];
        }

        $validIds = StockItem::whereIn('item_id', array_column($clean, 'item_id'))
            ->where('is_sales_item', 0)
            ->pluck('item_id')
            ->all();
        if (count($validIds) !== count($clean)) {
            return response()->json([
                'js_notifications' => ['error' => __('Select a valid product.')],
            ], 422);
        }

        DB::transaction(function () use ($service_id, $clean) {
            ServiceUsedProduct::where('service_id', $service_id)->delete();
            foreach ($clean as $row) {
                ServiceUsedProduct::create([
                    'service_id' => $service_id,
                    'item_id' => $row['item_id'],
                    'quantity' => $row['quantity'],
                ]);
            }
        });

        return response()->json([
            'gotourl' => route('tab.master.service.details', [
                'service_id' => $service_id,
                'tab_name' => $tab_name,
            ]),
        ]);
    }
}
