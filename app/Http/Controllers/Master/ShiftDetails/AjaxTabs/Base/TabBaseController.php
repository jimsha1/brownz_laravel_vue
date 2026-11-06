<?php

namespace App\Http\Controllers\Master\ShiftDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use DB;
class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.master.shift.details';
    private $module = 'Master';
    private $namespace = 'ShiftDetails';
    private $model = 'Shift';

    public function index($shift_id, $tab_name = null)
    {
       
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $shift_info = null;
        if ($shift_id > 0) {
            try {
                
                $shift_info = Shift::findOrFail($shift_id);
               
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('shift_id', $shift_id);

        $this->addDependencyInjection(\App\Repositories\ShiftRepository::class, 'shift_repo');

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
                'shift_id' => $shift_id,
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
            'href' => route('tab.master.shift.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('master.shift-details.index', array_merge($tabs_data, [
            'shift_info' => $shift_info,
            'page_name' =>'shift_details',
            'category_name' =>'master',
        ]));
    }
}
