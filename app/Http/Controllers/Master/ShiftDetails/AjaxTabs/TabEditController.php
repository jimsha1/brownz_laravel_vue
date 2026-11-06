<?php

namespace App\Http\Controllers\Master\ShiftDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ShiftRepository;
use App\Models\Shift;
use Auth;
use Illuminate\Support\Facades\DB;

class TabEditController extends Controller
{
    public function index(Request $request, ShiftRepository $shift_repo, $shift_id, $tab_name)
    {
        $shift_info = $this->findById($shift_id, $shift_repo);

        $attrs = $shift_info->getAttributes();
        $shiftPayload = $shift_info->toArray();
        $shiftPayload['is_active'] = ((int) $shift_id <= 0)
            ? 1
            : $this->resolveCheckboxFlag($attrs['is_active'] ?? 0);

        $cancelUrl = ((int) $shift_id > 0)
            ? route('tab.master.shift.details', ['shift_id' => $shift_id, 'tab_name' => 'info'])
            : route('tab.master.shift.list');

        $details_edit_vue = [
            'shift_id' => (int) $shift_id,
            'tab_name' => $tab_name,
            'shift_info' => $shiftPayload,
            'urls' => [
                'button_cancel_url' => $cancelUrl,
                'form_save_url' => route('tab.master.shift.save', ['shift_id' => $shift_id, 'tab_name' => $tab_name]),
            ],
        ];

        return view('master.shift-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, ShiftRepository $shift_repo, $shift_id, $tab_name)
    {
        if ($request->shift_id != $shift_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        $shift = $this->findById($shift_id, $shift_repo);

        // Apply validation
        $this->applyValidation($request);

        // Start database transaction
        $response = DB::transaction(function () use ($request, $shift) {
            return $this->saveFromRequest($request, $shift);
        });

        return response()->json([
            'gotourl' => route('tab.master.shift.details', [
                'shift_id' => $response->shift_id,
                'tab_name' => 'info',
            ])
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
    }

    private function saveFromRequest(Request $request, Shift $shift)
    {
        $shift->title = $request->title;
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        $shift->is_active = $this->resolveCheckboxFlag($request->input('is_active'));
        $shift->save();

        voco_flash()->success(__('Shift details updated!'));

        return $shift;
    }

    private function resolveCheckboxFlag($value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'on', 'yes'], true) ? 1 : 0;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }
}
