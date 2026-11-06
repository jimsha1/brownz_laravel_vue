<?php

namespace App\Http\Controllers\Master\OutletDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\OutletRepository;
use App\Models\Outlet;
use Auth;

class TabEditController extends Controller
{
    public function index(Request $request, OutletRepository $outlet_repo, $outlet_id, $tab_name)
    {
        $outlet_info = ($outlet_id > 0) ?
                $outlet_repo->model()->findOrFail($outlet_id) :
                $outlet_repo->model();

        $outlet_info->button_cancel_url = route('tab.master.outlet.list');
        $outlet_info->form_save_url = route('tab.master.outlet.save', ['outlet_id' => $outlet_id, 'tab_name' => $tab_name]);

        $details_edit_vue = [
            'outlet_id' => $outlet_id,
            'tab_name' => $tab_name,
            'outlet_info' => $outlet_info,
        ];

        return view('master.outlet-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, OutletRepository $outlet_repo, $outlet_id, $tab_name)
    {
        $form_outlet_id = $request->outlet_id;

        if ($form_outlet_id != $outlet_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for edit, or create a new model for insert
        $outlet = ($request->outlet_id > 0) ?
                $outlet_repo->model()->findOrFail($request->outlet_id) :
                $outlet_repo->model();

        // Apply validation
        $this->applyValidation($request);

        // Start database transaction
        $response = \DB::transaction(function () use ($request, $outlet) {
            return $this->saveFromRequest($request, $outlet);
        });

        return response()->json([
            'gotourl' => route('tab.master.outlet.details', [
                'outlet_id' => $response->outlet_id,
                'tab_name' => 'edit',
            ])
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'location' => 'required',
            'address' => 'required',
            'city' => 'required',
        ]);
    }

    private function saveFromRequest(Request $request, Outlet $outlet)
    {

        $outlet->name = $request->name;
        $outlet->phone = $request->phone;
        $outlet->email = $request->email;
        $outlet->location = $request->location;
        $outlet->address = $request->address;
        $outlet->city = $request->city;
        $outlet->is_active = (int) $request->is_active;

        $outlet->save();

        voco_flash()->success(__('Outlet details updated!'));

        return $outlet;
    }
}
