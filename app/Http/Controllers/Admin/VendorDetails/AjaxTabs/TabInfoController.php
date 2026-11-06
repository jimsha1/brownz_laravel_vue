<?php

namespace App\Http\Controllers\Admin\VendorDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\VendorRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, VendorRepository $vendor_repo, $vendor_id, $tab_name)
    {
        $vendor_info = $vendor_repo->model()
                ->with('createdUser')
                ->findOrFail($vendor_id);

        $details_info_vue = [
            'vendor_id' => $vendor_id,
            'tab_name' => $tab_name,
            'vendor_info' => $vendor_info,
        ];

        return view('admin.vendor-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
