<?php

namespace App\Http\Controllers\Admin\ClientDetails\AjaxTabs\Base;

use App\Http\Controllers\Controller;

class TabBaseController extends Controller
{
    use \VocomiApp\Http\Traits\AjaxTabbableTabs;
    use \VocomiApp\Http\Traits\AjaxTabbableDevelopmentEnv;

    /* Inheritable properties */
    protected $route_name = 'tab.admin.client.details';
    private $module = 'Admin';
    private $namespace = 'ClientDetails';
    private $model = 'Client';

    public function index($client_id, $tab_name = null)
    {
        // This should be the first statement for ajax tabs
        $this->initTabPaths();

        $client_info = null;
        if ($client_id > 0) {
            $client_info = \App\Models\Client::join('users', 'clients.client_id', '=', 'users.id')
                ->where('clients.client_id', $client_id)
                ->select(
                    'clients.*',
                    'users.name',
                    'users.mobile',
                    'users.email',
                    'users.user_status',
                    'users.address',
                    'users.gender'
                )
                ->first();

            // Check whether db record exists
            if (empty($client_info['client_id'])) {
                return $this->tabViewNotFound();
            }
        }

        // Add route params
        $this->addRouteParam('client_id', $client_id);

        $this->addDependencyInjection(\App\Repositories\ClientRepository::class, 'client_repo'   );

        // Add tabs
        $this->addTabs('info', 'fa fa-eye');
        $this->addTabs('edit', 'fa fa-edit', true);
        $this->addTabs('payment', 'fa fa-money');

        $selected_tab_name = $this->getSelectedTab($tab_name);

        // Be careful! this will auto generate controller and blade files,
        // based on the above tabs configured (when not in production environment)
        $this->ensureFiles(dirname(__DIR__));

        // Ajax request must be treated separately
        if (\Request::ajax()) {
            $tab_html = $this->renderView($this->module, $this->namespace, $selected_tab_name, [
                'client_id' => $client_id,
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
            'href' => route('tab.admin.client.list'),
        ]);

        // Activate tab via JS
        $this->activateViaJs(true);

        $tabs_data = $this->tabView($selected_tab_name);

        return view('admin.client-details.index', array_merge($tabs_data, [
            'client_info' => $client_info,
            'page_name' => 'client_details',
            'category_name' => 'salon',
        ]));
    }
}
