<?php

namespace App\Http\Controllers\Master\ServiceCategoryDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceCategoryRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, ServiceCategoryRepository $service_category_repo, $category_id, $tab_name)
    {
        $service_category_info = $service_category_repo->model()
            ->with('createdUser')
            ->with('updatedUser')
            ->findOrFail($category_id);

        $details_info_vue = [
            'category_id' => $category_id,
            'tab_name' => $tab_name,
            'service_category_info' => $service_category_info,
            'updated_at_label' => $service_category_info->updated_at
                ? $service_category_info->updated_at->format('d M Y, h:i A')
                : null,
        ];

        return view('master.service-category-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
