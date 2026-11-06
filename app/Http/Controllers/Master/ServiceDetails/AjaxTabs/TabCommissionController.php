<?php

namespace App\Http\Controllers\Master\ServiceDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Repositories\ServiceRepository;
use Validator;
use Auth;
use Carbon\Carbon;

class TabCommissionController extends Controller
{
    public function index(Request $request, ServiceRepository $service_repo, $service_id, $tab_name)
    {
        $service_info = Service::find($service_id);

        $details_edit_vue = [
            'service_id' => $service_id,
            'tab_name' => $tab_name,
            'service_info' => $service_info,
            'urls' => [
                'button_cancel_url' => route('tab.master.service.details', [
                    'service_id' => $service_id,
                    'tab_name' => 'info',
                ]),
                'form_save_url' => route('tab.master.service.save', ['service_id' => $service_id, 'tab_name' => $tab_name]),
            ],
        ];
        return view('master.service-details.ajax-tabs.tab-commission', compact('details_edit_vue'));
    }

    public function save(Request $request, ServiceRepository $service_repo, $service_id, $tab_name)
    {
        if ($request->service_id != $service_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        $service = Service::find($service_id);

        $service->start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $service->end_date = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
        $service->commission_percentage = $request->commission_percentage;
        $service->commission_status = $this->resolveCheckboxFlag($request->input('commission_status'));
        $service->save();

        return response()->json([
            'gotourl' => route('tab.master.service.details', [
                'service_id' => $service_id,
                'tab_name' => $tab_name,
            ])
        ]);
    }

    private function resolveCheckboxFlag($value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'on', 'yes'], true) ? 1 : 0;
    }
}
