<?php

namespace App\Http\Controllers\Master\ShiftDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ShiftRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, ShiftRepository $shift_repo, $shift_id, $tab_name)
    {
        $shift_info = $shift_repo->model()
                ->with('createdUser')
                ->with('updatedUser')
                ->findOrFail($shift_id);

        $details_info_vue = [
            'shift_id' => $shift_id,
            'tab_name' => $tab_name,
            'shift_info' => $shift_info,
            'updated_at_label' => $shift_info->updated_at
                ? $shift_info->updated_at->format('d M Y, h:i A')
                : null,
        ];

        return view('master.shift-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
