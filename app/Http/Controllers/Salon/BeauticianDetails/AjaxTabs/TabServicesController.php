<?php

namespace App\Http\Controllers\Salon\BeauticianDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BeauticianService;
use App\Models\Service;
use App\Repositories\BeauticianRepository;

class TabServicesController extends Controller
{
    public function index(Request $request, BeauticianRepository $beautician_repo, $beautician_id, $tab_name)
    {
        $db_records = Service::with(['category.parent'])
            ->when(
                \Schema::hasColumn((new Service)->getTable(), 'is_active'),
                function ($query) {
                    $query->where('is_active', 1);
                }
            )
            ->orderBy('name')
            ->get();

        $assigned_services = BeauticianService::where('beautician_id', $beautician_id)
            ->pluck('service_id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->values()
            ->toArray();

        $details_service_vue = [
            'beautician_id' => $beautician_id,
            'tab_name' => $tab_name,
            'assigned_services' => $assigned_services,
            'db_all_services' => $db_records,
            'button_cancel_url' => route('tab.salon.beautician.list'),
            'form_save_url' => route('tab.salon.beautician.save', [
                'beautician_id' => $beautician_id,
                'tab_name' => $tab_name,
            ]),
        ];

        return view('salon.beautician-details.ajax-tabs.tab-services', compact('details_service_vue'));
    }

    public function save(Request $request, BeauticianRepository $beautician_repo, $beautician_id, $tab_name)
    {
        if ($request->beautician_id != $beautician_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        BeauticianService::where('beautician_id', $beautician_id)->delete();

        if ($request->has('services')) {
            foreach ($request->services as $service_id) {
                $beautician_service = new BeauticianService();
                $beautician_service->beautician_id = $beautician_id;
                $beautician_service->service_id = $service_id;
                $beautician_service->save();
            }
        }

        voco_flash()->success(__('Beautician services updated!'));

        return response()->json([
            'gotourl' => route('tab.salon.beautician.details', [
                'beautician_id' => $beautician_id,
                'tab_name' => $tab_name,
            ]),
        ]);
    }
}
