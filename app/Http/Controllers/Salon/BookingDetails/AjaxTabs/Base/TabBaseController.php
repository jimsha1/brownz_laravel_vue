<?php

namespace App\Http\Controllers\Salon\BookingDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Repositories\BookingRepository;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.salon.booking.details';
    private $module = 'Salon';
    private $namespace = 'BookingDetails';
    private $model = 'Booking';

    public function index(BookingRepository $booking_repo, $booking_id, $tab_name = null)
    {

        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $booking_info = null;
        if ($booking_id > 0) {
            try {
                $booking_info = $booking_repo->hunter()->findBooking($booking_id);

            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('booking_id', $booking_id);

        $this->addDependencyInjection(\App\Repositories\BookingRepository::class, 'booking_repo');

        // Add tabs
        if ($booking_id > 0) {
            $this->addTabs('info', 'fa fa-eye');
        }

        $this->addTabs('edit', 'fa fa-edit', true);

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'booking_id' => $booking_id,
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

        // New booking button
        $this->addTabButton('a', __('New'), 'fa fa-plus', [
            'class' => 'btn_dark-x',
            'href' => route('tab.salon.booking.details', ['booking_id' => '0', 'tab_name' => 'edit']),
            'data-toggle' => 'tooltip',
            'title' => 'Create New Booking',
        ]);

        // Tab back button
        $this->addTabButton('a', __('Back'), 'fa fa-backward', [
            'class' => 'btn_dark-x vctab-btn',
            'href' => route('tab.salon.booking.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('salon.booking-details.index', array_merge($tabs_data, [
            'booking_info' => $booking_info,
            'page_name' =>'booking_details',
            'category_name' =>'salon',

        ]));
    }
}
