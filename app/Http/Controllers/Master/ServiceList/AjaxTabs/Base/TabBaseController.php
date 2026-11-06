<?php

namespace App\Http\Controllers\Master\ServiceList\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.master.service.list';
    private $module = 'Master';
    private $namespace = 'ServiceList';
    private $model = 'Service';

    public function index($tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $this->addDependencyInjection(\App\Repositories\ServiceRepository::class, 'service_repo');

        // Add tabs
        $this->addTabs('all', 'fa fa-folder-open-o');

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'tab_name' => $selected_tab_name,
            ]);

            if ($tab_html instanceof \Illuminate\Http\JsonResponse) {
                return $tab_html;
            }

            return response()->json([
                'tab_name' => $selected_tab_name,
                'ajax_tab_html' => $tab_html
            ]);
        }

        // Buttons for navigation
        $this->addTabButton('button', __('New'), 'fa fa-plus', [
            'type' => 'button',
            'class' => 'btn btn-sm btn-primary vctab-btn btn_dark-x',
            'data-modal-wide' => 'true',
            'data-url' => route('tab.master.service.details', ['service_id' => '0', 'tab_name' => 'edit', 'return_html' => 'y']),
            'data-modal-title' => __('Create New Service'),
            'data-voco-toggle' => 'modal',
            'data-target' => '#voco-ajax-modal',
            'data-toggle' => 'tooltip',
            'title' => __('Create New Service'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        $parentCategories = ServiceCategory::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['category_id', 'name']);

        return view('master.service-list.index', array_merge($tabs_data, [
            'parentCategories' => $parentCategories,
        ]));
    }

    public function fetchSubcategories($parent_id)
    {
        $subcategories = ServiceCategory::getSubcategories($parent_id);
        return response()->json($subcategories);
    }
}
