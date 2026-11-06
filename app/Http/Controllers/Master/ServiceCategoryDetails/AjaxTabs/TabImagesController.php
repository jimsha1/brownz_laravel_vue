<?php

namespace App\Http\Controllers\Master\ServiceCategoryDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceCategoryRepository;
use App\Models\ServiceCategory as Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class TabImagesController extends Controller
{
    public function index(Request $request, ServiceCategoryRepository $category_repo, $category_id, $tab_name)
    {
        $category_info = $category_repo->model()->findOrFail($category_id);

       // $category_info->banner_image_url = category_banner_image_url($category_info->banner_image, 1000, 150, 0);
        //$category_info->square_image_url = category_square_image_url($category_info->square_image, 300, 300, 0);
        $category_info->banner_image_url = URL::to('/').'/'.$category_info->banner_image;
        $category_info->square_image_url = URL::to('/').'/'.$category_info->square_image;

        $details_images_vue = [
            'category_id' => $category_id,
            'base_url' => URL::to('/'),
            'tab_name' => $tab_name,
            'category_info' => $category_info,
            'urls' => [
                'form_save_url' => route('tab.master.service.category.save', ['category_id' => $category_id, 'tab_name' => $tab_name]),
            ],
        ];

        return view('master.service-category-details.ajax-tabs.tab-images', compact('details_images_vue'));
    }

    public function save(Request $request, ServiceCategoryRepository $category_repo, $category_id, $tab_name)
    {
       
        if ($request->category_id != $category_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update
        $category = $category_repo->model()->findOrFail($category_id);

        // Start database transaction
        $response = DB::transaction(fn() => $this->saveFromRequest($request, $category, $category_repo));

        return redirect()->route('tab.master.service.category.details', [
            'category_id' => $response->category_id,
            'tab_name' => $tab_name,
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function saveFromRequest(Request $request, Category $category, ServiceCategoryRepository $category_repo)
    {
        $result = $category_repo->uploadCategoryImage($category, $request->file('category_image_file'), $request->image_target);

        if ($result) {
            voco_flash()->success(__('Category image updated!'));
        } else {
            voco_flash()->danger(__('Something went wrong while updating category image. Please try again later.'));
        }

        return $category;
    }
}
