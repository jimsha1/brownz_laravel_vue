<?php

namespace App\Http\Controllers\Master\GlobalTaxDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\GlobalTaxRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, GlobalTaxRepository $global_tax_repo, $tax_id, $tab_name)
    {
        $global_tax_info = $global_tax_repo->model()
                ->with('createdUser')
                ->findOrFail($tax_id);

        $details_info_vue = [
            'tax_id' => $tax_id,
            'tab_name' => $tab_name,
            'global_tax_info' => $global_tax_info,
            'created_at_label' => $global_tax_info->created_at
                ? $global_tax_info->created_at->format('d M Y, h:i A')
                : null,
        ];

        return view('master.global-tax-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
