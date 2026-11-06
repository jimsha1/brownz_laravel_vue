<?php

namespace App\Http\Controllers\Admin\ClientDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ClientRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, ClientRepository $client_repo, $client_id, $tab_name)
    {
        $client_info = $client_repo->getClient($client_id);

        if (empty($client_info)) {
            return view('admin.client-details.ajax-tabs.404-not-found');
        }

        $details_info_vue = [
            'client_id' => $client_id,
            'tab_name' => $tab_name,
            'client_info' => $client_info,
        ];

        return view('admin.client-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
