<?php

namespace App\Http\Controllers\Master\ServiceCategoryDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceCategoryRepository;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;

class TabWebContentController extends Controller
{
    public function index(Request $request, ServiceCategoryRepository $category_repo, $category_id, $tab_name)
    {
        $category_info = $this->findById($category_id, $category_repo);

        $details_web_content_vue = [
            'category_id' => $category_id,
            'tab_name' => $tab_name,
            'category_info' => $category_info,
            'urls' => [
                'button_cancel_url' => route('tab.master.service.category.list'),
                'form_save_url' => route('tab.master.service.category.save', ['category_id' => $category_id, 'tab_name' => $tab_name]),
            ],
        ];

        return view('master.service-category-details.ajax-tabs.tab-web-content', compact('details_web_content_vue'));
    }

    public function save(Request $request, ServiceCategoryRepository $category_repo, $category_id, $tab_name)
    {
        if ($request->category_id != $category_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        $category = $this->findById($category_id, $category_repo);

        // Start database transaction
        $response = DB::transaction(fn() => $this->saveFromRequest($request, $category));

        return response()->json([
            'gotourl' => route('tab.master.service.category.details', [
                'category_id' => $response->category_id,
                'tab_name' => $tab_name,
            ])
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function saveFromRequest(Request $request, ServiceCategory $category)
    {
        $category->tagline_en = $request->tagline_en;
        $category->tagline_ar = $request->tagline_ar;

        $category->description_en = $request->description_en;
        $category->description_ar = $request->description_ar;

        $category->long_description_en = $request->long_description_en;
        $category->long_description_ar = $request->long_description_ar;

        $category->save();

        voco_flash()->success(__('Service category details updated!'));

        return $category;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }
}
