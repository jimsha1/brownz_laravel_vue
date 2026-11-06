<?php

namespace App\Http\Controllers\Salon\BeauticianDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\BeauticianRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, BeauticianRepository $beautician_repo, $beautician_id, $tab_name)
    {
        $beautician_info = $beautician_repo->getBeautician($beautician_id);

        if (empty($beautician_info)) {
            return view('salon.beautician-details.ajax-tabs.404-not-found');
        }

        $beautician_info->photo_src_url = beautician_photo_url($beautician_info->profile_photo_path, 150, 150, 0);
        $beautician_info->is_active = ($beautician_info->status ?? null) === \App\Models\Beautician::STATUS_ACTIVE
            && ($beautician_info->employee_status ?? null) === 'active'
            && ($beautician_info->user_status ?? null) === 'active';

        $details_info_vue = [
            'beautician_id' => $beautician_id,
            'member_id'=>'',
            'tab_name' => $tab_name,
            'beautician_info' => $beautician_info,
        ];

        return view('salon.beautician-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
