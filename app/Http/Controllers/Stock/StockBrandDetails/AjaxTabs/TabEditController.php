<?php

namespace App\Http\Controllers\Stock\StockBrandDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\StockBrand;
use App\Repositories\StockBrandRepository;
use App\Service\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabEditController extends Controller
{
    public function index(Request $request, StockBrandRepository $stock_brand_repo, $brand_id, $tab_name)
    {
        $stock_brand_info = $this->findById($brand_id, $stock_brand_repo);

        if ($stock_brand_info->exists) {
            $stock_brand_info->is_active = (int) $stock_brand_info->is_active;
        } else {
            $stock_brand_info->is_active = 1;
        }

        $details_edit_vue = [
            'brand_id' => $brand_id,
            'tab_name' => $tab_name,
            'stock_brand_info' => $stock_brand_info,
            'urls' => [
                'button_cancel_url' => route('tab.stock.stock.brand.list'),
                'form_save_url' => route('tab.stock.stock.brand.save', [
                    'brand_id' => $brand_id,
                    'tab_name' => $tab_name,
                ]),
            ],
        ];

        return view('stock.stock-brand-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, StockBrandRepository $stock_brand_repo, EmployeeService $employeeService, $brand_id, $tab_name)
    {
        if ($request->brand_id != $brand_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        $stock_brand = $this->findById($brand_id, $stock_brand_repo);

        $this->applyValidation($request, $brand_id);

        if ((int) $brand_id > 0) {
            $request->validate([
                'auth_password' => 'required|string',
            ], [
                'auth_password.required' => __('Password is required to update this stock brand.'),
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

        DB::transaction(function () use ($request, $stock_brand) {
            return $this->saveFromRequest($request, $stock_brand);
        });

        return response()->json([
            'gotourl' => route('tab.stock.stock.brand.list'),
        ]);
    }

    private function applyValidation(Request $request, $brand_id = 0)
    {
        $primaryKey = (new StockBrand())->getKeyName();

        $request->validate([
            'brand_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request, $brand_id, $primaryKey) {
                    $existingBrand = StockBrand::whereRaw('LOWER(brand_name) = LOWER(?)', [$request->brand_name]);

                    if ($brand_id > 0) {
                        $existingBrand->where($primaryKey, '!=', $brand_id);
                    }

                    if ($existingBrand->exists()) {
                        $fail(__('The brand name already exists. Please use a different name.'));
                    }
                },
            ],
            'is_active' => 'nullable',
        ]);
    }

    private function saveFromRequest(Request $request, StockBrand $stock_brand)
    {
        $stock_brand->brand_name = $request->brand_name;
        $stock_brand->is_active = (int) $request->is_active === 1 ? 1 : 0;
        $stock_brand->save();

        voco_flash()->success(__('Stock brand details updated!'));

        return $stock_brand;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }
}
