<?php

namespace App\Http\Controllers\Accounts\AccountDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AccountRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, AccountRepository $account_repo, $account_id, $tab_name)
    {
        $account_info = $account_repo->model()
                ->with('createdUser')
                ->findOrFail($account_id);

        $details_info_vue = [
            'account_id' => $account_id,
            'tab_name' => $tab_name,
            'account_info' => $account_info,
        ];

        return view('accounts.account-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
