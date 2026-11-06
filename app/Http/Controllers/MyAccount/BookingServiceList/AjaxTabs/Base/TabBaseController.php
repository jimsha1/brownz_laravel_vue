<?php

namespace App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs\Base;

use App\Http\Controllers\Controller;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.my.account.booking.service.list';
    private $module = 'MyAccount';
    private $namespace = 'BookingServiceList';
    private $model = 'BookingService';

    public function index($tab_name = null)
    {
       
      //dd('dddddddddddd');
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $this->addDependencyInjection(\App\Repositories\BookingServiceRepository::class, 'booking_service_repo');

        // Add tabs
        $this->addTabs('upcoming', 'fa fa-hourglass-start');
        $this->addTabs('on-going', 'fa fa-hourglass-half');
        $this->addTabs('completed', 'fa fa-hourglass-end');
        $this->addTabs('expired', 'fa fa-hourglass-o');
        $this->addTabs('all', 'fa fa-calendar');

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

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('my-account.booking-service-list.index', $tabs_data);
    }
}
