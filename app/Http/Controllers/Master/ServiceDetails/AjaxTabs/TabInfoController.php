<?php

namespace App\Http\Controllers\Master\ServiceDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, ServiceRepository $service_repo, $service_id, $tab_name)
    {
        $service_info = $service_repo->model()
                ->with('createdUser')
                ->with('updatedUser')
                ->with('category.parent')
                ->findOrFail($service_id);

        $details_info_vue = [
            'service_id' => $service_id,
            'tab_name' => $tab_name,
            'service_info' => $service_info,
            'updated_at_label' => $service_info->updated_at
                ? $service_info->updated_at->format('d M Y, h:i A')
                : null,
        ];

        return view('master.service-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
