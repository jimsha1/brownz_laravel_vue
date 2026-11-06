<?php

namespace App\Http\Controllers\Master\GlobalTaxDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\GlobalTaxRepository;
use App\Models\GlobalTax;
use Auth;
use Illuminate\Support\Facades\DB;

class TabEditController extends Controller
{
    public function index(Request $request, GlobalTaxRepository $global_tax_repo, $tax_id, $tab_name)
    {
        $global_tax_info = $this->findById($tax_id, $global_tax_repo);

        if ((int) $tax_id <= 0 && $global_tax_info->is_active === null) {
            $global_tax_info->is_active = 1;
        }

        // Normalize for Vue checkbox true-value/false-value ("1"/"0")
        if ($global_tax_info->exists || (int) $tax_id > 0) {
            $global_tax_info->is_active = (int) $global_tax_info->is_active === 1 ? '1' : '0';
        } else {
            $global_tax_info->is_active = '1';
        }

        $cancelUrl = ((int) $tax_id > 0)
            ? route('tab.master.global.tax.details', ['tax_id' => $tax_id, 'tab_name' => 'info'])
            : route('tab.master.global.tax.list');

        $details_edit_vue = [
            'tax_id' => (int) $tax_id,
            'tab_name' => $tab_name,
            'global_tax_info' => $global_tax_info,
            'urls' => [
                'button_cancel_url' => $cancelUrl,
                'form_save_url' => route('tab.master.global.tax.save', ['tax_id' => $tax_id, 'tab_name' => $tab_name]),
            ],
        ];

        return view('master.global-tax-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, GlobalTaxRepository $global_tax_repo, $tax_id, $tab_name)
    {
        if ($request->tax_id != $tax_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        $global_tax = $this->findById($tax_id, $global_tax_repo);

        // Apply validation
        $this->applyValidation($request);

        // Start database transaction
        $response = DB::transaction(function () use ($request, $global_tax) {
            return $this->saveFromRequest($request, $global_tax);
        });

        return response()->json([
            'gotourl' => route('tab.master.global.tax.details', [
                'tax_id' => $response->tax_id,
                'tab_name' => $tab_name,
            ])
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request)
    {
        $request->validate([

            'name' => 'required|unique:global_taxes,name,' . $request->tax_id . ',tax_id',
            'tax' => 'required|numeric|between:0.01,99.99',
        ], [
            'name.unique' => __('Tax name already exists.'),
        ]);
    }

    private function saveFromRequest(Request $request, GlobalTax $global_tax)
    {
        $global_tax->name = $request->name;
        $global_tax->tax = $request->tax;
        $global_tax->is_active = $this->resolveIsActive($request->input('is_active'));
        $global_tax->save();

        voco_flash()->success(__('Global tax details updated!'));

        return $global_tax;
    }

    private function resolveIsActive($value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'on', 'yes'], true) ? 1 : 0;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }
}
