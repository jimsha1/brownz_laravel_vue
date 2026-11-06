<?php

namespace App\Http\Controllers\Master\ServiceCategoryDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceCategoryRepository;
use App\Models\ServiceCategory;
use Auth;

class TabEditController extends Controller
{
    public function index(Request $request, ServiceCategoryRepository $service_category_repo, $category_id, $tab_name)
    {

        $service_category_info = $this->findById($category_id, $service_category_repo);

        // Plain array so boolean casts don't break Vue checkbox true-value matching
        $categoryPayload = $service_category_info->toArray();
        $categoryPayload['show_on_website'] = !empty($service_category_info->show_on_website) ? 1 : 0;
        $categoryPayload['is_active'] = ((int) $category_id <= 0)
            ? 1
            : (!empty($service_category_info->is_active) ? 1 : 0);

        $cancelUrl = ((int) $category_id > 0)
            ? route('tab.master.service.category.details', ['category_id' => $category_id, 'tab_name' => 'info'])
            : route('tab.master.service.category.list');

        $details_edit_vue = [
            'category_id' => (int) $category_id,
            'tab_name' => $tab_name,
            'service_category_info' => $categoryPayload,
            'urls' => [
                'button_cancel_url' => $cancelUrl,
                'form_save_url' => route('tab.master.service.category.save', ['category_id' => $category_id, 'tab_name' => $tab_name]),
            ],
            'dropdowns' => [
                'parent_categories' => \Facades\App\Models\ServiceCategory::parentCategoriesDropdownJson(),
            ],
        ];

        return view('master.service-category-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, ServiceCategoryRepository $service_category_repo, $category_id, $tab_name)
    {
        if ($request->category_id != $category_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        $service_category = $this->findById($category_id, $service_category_repo);

        // Apply validation
        $this->applyValidation($request);

        // Start database transaction
        $response =  $this->saveFromRequest($request, $service_category);

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

    private function applyValidation(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
    }

    private function saveFromRequest(Request $request, ServiceCategory $service_category)
    {
        $service_category->parent_id = $request->parent_id;
        $service_category->name = $request->name;
        $service_category->show_on_website = $this->resolveCheckboxFlag($request->input('show_on_website'));
        $service_category->is_active = $this->resolveCheckboxFlag($request->input('is_active'));
        $service_category->save();

        voco_flash()->success(__('Service category details updated!'));

        return $service_category;
    }

    private function resolveCheckboxFlag($value): int
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
