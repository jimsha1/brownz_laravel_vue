<?php

namespace App\Http\Controllers\Salon\BeauticianList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Beautician;
use App\Models\Shift;
use App\Repositories\BeauticianRepository;

class TabAllController extends Controller
{
    public function index(Request $request, BeauticianRepository $beautician_repo, $tab_name)
    {
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        $status = $request->status ?: 'active';

        $db_records = $beautician_repo->hunter()
            ->setSearchTerm($request->search)
            ->setStatus($status === 'all' ? null : $status)
            ->search();

        // Add additional properties for the records
        foreach ($db_records as $row) {
            $row->edit_url = route('tab.salon.beautician.details', [
                'beautician_id' => $row->beautician_id,
                'tab_name' => 'edit',
            ]);
            $row->view_url = route('tab.salon.beautician.details', [
                'beautician_id' => $row->beautician_id,
                'tab_name' => 'info',
            ]);
            $row->photo_src_url = beautician_photo_url($row->profile_photo_path, 40, 40, 1);

            $isActive = ($row->beautician_status ?? null) === Beautician::STATUS_ACTIVE
                && ($row->employee_status ?? null) === 'active'
                && ($row->user_status ?? null) === 'active';
            $row->is_active = $isActive;
            $row->status_label = $isActive ? 'active' : 'inactive';
        }

        $shift = Shift::all();
        $db_results = [
            'vue_data' => [
                'vue_list_tab_all' => [
                    'paginator' => paginator_vue_data($db_records),
                    'shift' => [],
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ],
        ];

        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('salon.beautician-list.ajax-tabs.tab-all', compact('db_results', 'tab_name', 'shift'));
    }
}
