<?php

namespace App\Http\Controllers\Salon\BeauticianDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.salon.beautician.details';
    private $module = 'Salon';
    private $namespace = 'BeauticianDetails';
    private $model = 'Beautician';

    public function index($beautician_id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $beautician_info = null;
        if ($beautician_id > 0) {
            $beautician_info = \App\Models\Beautician::join('users', 'beauticians.beautician_id', '=', 'users.id')
                ->leftJoin('employees', 'employees.user_id', '=', 'beauticians.beautician_id')
                ->where('beauticians.created_outlet_id', session_outlet_id())
                ->where('beauticians.beautician_id', $beautician_id)
                ->select(
                    'beauticians.*',
                    'users.name',
                    'users.mobile',
                    'users.profile_photo_path',
                    'users.user_status',
                    'employees.status as employee_status'
                )
                ->first();

            // Check whether db record exists
            if (empty($beautician_info['beautician_id'])) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('beautician_id', $beautician_id);

        $this->addDependencyInjection(\App\Repositories\BeauticianRepository::class, 'beautician_repo');

        // Add tabs
        $this->addTabs('info', 'fa fa-eye');
        $this->addTabs('edit', 'fa fa-edit', true);
        $this->addTabs('services', 'fa fa-list');

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'beautician_id' => $beautician_id,
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
            'href' => route('tab.salon.beautician.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('salon.beautician-details.index', array_merge($tabs_data, [
            'beautician_info' => $beautician_info,
            'page_name' =>'salon_beautician_details',
            'category_name' =>'salon',
        ]));
    }
}
