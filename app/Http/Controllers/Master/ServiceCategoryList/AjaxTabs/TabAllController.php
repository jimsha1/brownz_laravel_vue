<?php

namespace App\Http\Controllers\Master\ServiceCategoryList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceCategoryRepository;
use Auth;

class TabAllController extends Controller
{
    public function index(Request $request, ServiceCategoryRepository $service_category_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $status = $request->status ?: 'all';
        $showOnWebsite = $request->show_on_website ?: 'all';
        $parentId = $request->parent_id ?: 'all';

        $db_records = $service_category_repo->hunter()
            ->query(function ($query) {
                $query->with('parent');
            })
            ->setSearchTerm($request->search)
            ->setStatus($status === 'all' ? null : $status)
            ->setShowOnWebsite($showOnWebsite === 'all' ? null : $showOnWebsite)
            ->setParentId($parentId === 'all' ? null : $parentId)
            ->search();

        // Add additional properties for the records
        foreach ($db_records as $row) {
            $row->edit_url = route('tab.master.service.category.details', ['category_id' => $row->category_id, 'tab_name' => 'edit']);
            $row->view_url = route('tab.master.service.category.details', ['category_id' => $row->category_id, 'tab_name' => 'info']);

            if ($row->square_image != null) {
                $row->square_image = trim($row->square_image, 'services/');
            }
            $row->square_image_url = category_square_image_url($row->square_image, 40, 40, 1);
        }

        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_all' => [
                    'paginator' => paginator_vue_data($db_records),
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ]
        ];

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('master.service-category-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
