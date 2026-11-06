<?php

namespace App\Http\Controllers\Admin\VendorDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\Vendor;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.admin.vendor.details';
    private $module = 'Admin';
    private $namespace = 'VendorDetails';
    private $model = 'Vendor';

    public function index($vendor_id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $vendor_info = null;
        if ($vendor_id > 0) {
            try {
                $vendor_info = Vendor::findOrFail($vendor_id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('vendor_id', $vendor_id);

        $this->addDependencyInjection(\App\Repositories\VendorRepository::class, 'vendor_repo');

        // Add tabs
        $this->addTabs('info', 'fa fa-eye');
        $this->addTabs('edit', 'fa fa-edit', true);

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'vendor_id' => $vendor_id,
                'tab_name' => $selected_tab_name,
            ]);

            if ($tab_html instanceof \Illuminate\Http\JsonResponse) {
                return $tab_html;
            }

            if ('y' === request('return_html')) {
                return $tab_html;
            }

            return response()->json([
                'tab_name' => $selected_tab_name,
                'ajax_tab_html' => $tab_html
            ]);
        }

        // Tab back button
        $this->addTabButton('a', __('Back'), 'fa fa-backward', [
            'class' => 'btn btn-sm btn-primary vctab-btn btn_dark-x',
            'href' => route('tab.admin.vendor.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('admin.vendor-details.index', array_merge($tabs_data, [
            'vendor_info' => $vendor_info,
            'category_name' =>'master',
            'page_name' =>'vendor_details',
        ]));
    }
}
