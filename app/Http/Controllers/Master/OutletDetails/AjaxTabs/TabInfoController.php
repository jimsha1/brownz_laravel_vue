<?php

namespace App\Http\Controllers\Master\OutletDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OutletRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, OutletRepository $outlet_repo, $outlet_id, $tab_name)
    {
        $outlet_info = $outlet_repo->model()
                ->with('createdUser')
                ->with('updatedUser')
                ->findOrFail($outlet_id);

        $details_info_vue = [
            'outlet_id' => $outlet_id,
            'tab_name' => $tab_name,
            'outlet_info' => $outlet_info,
        ];

        return view('master.outlet-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
