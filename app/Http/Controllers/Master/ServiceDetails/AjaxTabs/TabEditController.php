<?php

namespace App\Http\Controllers\Master\ServiceDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceRepository;
use App\Models\Service;
use App\Models\Shift;
use App\Models\ServiceTypeManagement;
use App\Models\PriceService;
use App\Models\ServiceCategory;
use Auth;
use Illuminate\Support\Facades\DB;

class TabEditController extends Controller
{
    public function index(Request $request, ServiceRepository $service_repo, $service_id, $tab_name)
    {
        $service_info = $this->findById($service_id, $service_repo);

        // Use raw attributes so boolean casts cannot confuse Vue checkbox binding
        $attrs = $service_info->getAttributes();
        $servicePayload = $service_info->toArray();
        $servicePayload['show_on_website'] = $this->normalizeFlag($attrs['show_on_website'] ?? 0);
        $servicePayload['is_active'] = ((int) $service_id <= 0)
            ? 1
            : $this->normalizeFlag($attrs['is_active'] ?? 0);
        $servicePayload['allow_online_booking'] = $this->normalizeFlag($attrs['allow_online_booking'] ?? 0);

        $cancelUrl = $service_id > 0
            ? route('tab.master.service.details', ['service_id' => $service_id, 'tab_name' => 'info'])
            : route('tab.master.service.list');

        $details_edit_vue = [
            'service_id' => $service_id,
            'tab_name' => $tab_name,
            'service_info' => $servicePayload,
            'durations_data' => \Facades\App\Models\Service::durationStaticData(),

            'urls' => [
                'button_cancel_url' => $cancelUrl,
                'form_save_url' => route('tab.master.service.save', ['service_id' => $service_id, 'tab_name' => $tab_name]),
            ],
            'dropdowns' => [
                'parent_categories' => \Facades\App\Models\ServiceCategory::parentCategoriesDropdownJson(),
                // 'service_categories' => \Facades\App\Models\ServiceCategory::serviceCategoriesDropdownJson(),
                'global_taxes' => \Facades\App\Models\GlobalTax::globalTaxesDropdownJson(),
                'all_branch' => \Facades\App\Models\Outlet::outletDropdownJson(),
            ],

        ];
        $shift_data = Shift::all();
        $service_type = ServiceTypeManagement::all();
        $duration_data = \Facades\App\Models\Service::durationStaticData();
        $service_shifts = PriceService::where('service_id', $service_id)->get();
        $shift_count = $service_shifts->count();
        return view('master.service-details.ajax-tabs.tab-edit', compact(
            'details_edit_vue',
            'shift_data',
            'service_type',
            'duration_data',
            'service_id',
            'shift_count',
            'service_shifts'
        ));
    }

    public function save(Request $request, ServiceRepository $service_repo, $service_id, $tab_name)
    {

        if ($request->service_id != $service_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        $service = $this->findById($service_id, $service_repo);

        // Apply validation
        $this->applyValidation($request);

        $isOKToProceed = true;
        $shift_count = $request->count_shift;
        if ($shift_count > 0) {
            for ($i = 1; $i <= $shift_count; $i++) {

                $shiftId = $request->get('shift_id_' . $i);
                $serviceTypeId = $request->get('service_type_id_' . $i);
                $duration = $request->get('duration_' . $i);

                for ($j = 1; $j <= $shift_count; $j++) {
                    if ($i != $j) {
                        if ($shiftId == $request->get('shift_id_' . $j) && $serviceTypeId == $request->get('service_type_id_' . $j)  && $duration == $request->get('duration_' . $j)) {
                            $isOKToProceed = false;
                        }
                    }
                }
            }
        }


        if ($isOKToProceed) {
            // Start database transaction
            $response = DB::transaction(function () use ($request, $service) {
                return $this->saveFromRequest($request, $service);
            });
        } else {
            voco_flash()->danger(__('Service price already exist!'));
        }

        // // Start database transaction
        // $response = \DB::transaction(function () use ($request, $service) {
        //     return $this->saveFromRequest($request, $service);
        // });

        return response()->json([
            'gotourl' => route('tab.master.service.details', [
                'service_id' => $response->service_id,
                'tab_name' => $tab_name,
            ])
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request)
    {
        $request->validate([
            'parent_category_id' => 'required',
            'category_id' => 'required',
            'name' => 'required',
            'duration_minutes' => 'required',
            'service_base_price' => 'required',
            'tax_percent' => 'required',
        ]);
    }

    private function saveFromRequest(Request $request, Service $service)
    {

        $service->category_id = $request->category_id;
        $service->parent_category_id = $request->parent_category_id;

        $service->name = $request->name;
        // $service->name_arabic = $request->name_arabic;

        $service->short_description_en = $request->short_description_en;
        // $service->short_description_ar = $request->short_description_ar;

        $service->duration_minutes = $request->duration_minutes;

        $service->service_base_price = $request->service_base_price;
        // $service->service_home_price = $request->service_home_price;

        if ($request->tax_percent > 0) {
            $service->tax_percent = $request->tax_percent;

            $service->service_base_total = percentage_increase($service->service_base_price, $request->tax_percent);
            // $service->service_home_total = percentage_increase($service->service_home_price, $request->tax_percent);
        }
        $service->outlet_id = $request->outlet_id;
        $service->allow_online_booking = $this->resolveCheckboxFlag($request->input('allow_online_booking'));
        $service->show_on_website = $this->resolveCheckboxFlag($request->input('show_on_website'));
        $service->is_active = $this->resolveCheckboxFlag($request->input('is_active'));
        $service->save();
        if ($service->save()) {

            $shift_count = $request->count_shift;
            $saveShift = "";
            // if ($shift_count == null) {
            if ($request->service_id > 0) {

                $isExist = PriceService::select("*")
                    ->where("service_id", $request->service_id)
                    ->exists();
                if ($isExist) {
                    DB::table('price_services')->where('service_id', $request->service_id)->delete();
                }
                // for ($i = 1; $i <= $shift_count; $i++) {
                $saveShift = new PriceService();
                $saveShift->service_id = $request->service_id;
                $saveShift->shift_id = 19;
                $saveShift->service_type_id = 1;
                $saveShift->duration = $request->duration_minutes;
                $saveShift->price = $request->service_base_price;
                $saveShift->save();
                // }
            } else {
                $last_row = DB::table('services')->orderBy('service_id', 'DESC')->first();
                //for ($i = 1; $i <= $shift_count; $i++) {
                $saveShift = new PriceService();
                $saveShift->service_id = $last_row->service_id;
                $saveShift->shift_id = 19;
                $saveShift->service_type_id = 1;
                $saveShift->duration = $request->duration_minutes;
                $saveShift->price = $request->service_base_price;
                $saveShift->save();
                //}
            }
            //}
            // dd($saveShift);
            if (!$saveShift) {
                voco_flash()->success(__('Service details updated!'));
            }
        } else {
            voco_flash()->danger(__('Service details failed!'));
        }


        return $service;
    }

    private function resolveCheckboxFlag($value): int
    {
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        $normalized = strtolower(trim((string) $value));

        return in_array($normalized, ['1', 'true', 'on', 'yes'], true) ? 1 : 0;
    }

    private function normalizeFlag($value): int
    {
        return $this->resolveCheckboxFlag($value);
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }

    // public function getSubcategories(Request $request)
    // {

    //     $parentId = $request->get('parent_id');
    //     $subcategories = ServiceCategory::where('parent_id', $parentId)
    //         ->where('is_active', 1)->get();

    //     return  $subcategories;
    // }


    public function getSubcategories(Request $request)
    {
        $parentId = $request->input('parent_id');

        $subcategories = DB::table('service_categories')
            ->select('name as text', 'category_id as id')
            ->where('parent_id', $parentId)
            ->where('is_active', 1)
            ->get();
        return response()->json($subcategories);
    }
}
