<?php

namespace App\Http\Controllers\Admin\VendorDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\VendorRepository;
use App\Models\Vendor;
use App\Service\AccountingService;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class TabEditController extends Controller
{
    public function index(Request $request, VendorRepository $vendor_repo, $vendor_id, $tab_name)
    {
        $vendor_info = $this->findById($vendor_id, $vendor_repo);

        $details_edit_vue = [
            'vendor_id' => $vendor_id,
            'tab_name' => $tab_name,
            'vendor_info' => $vendor_info,
            'urls' => [
                'button_cancel_url' => route('tab.admin.vendor.list'),
                'form_save_url' => route('tab.admin.vendor.save', ['vendor_id' => $vendor_id, 'tab_name' => $tab_name]),
            ],
        ];

        return view('admin.vendor-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, VendorRepository $vendor_repo, $vendor_id, $tab_name)
    {
        if ($request->vendor_id != $vendor_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        $vendor = $this->findById($vendor_id, $vendor_repo);

        // Apply validation
        $this->applyValidation($request, $vendor_id);

        // Start database transaction
        try {
            $response = DB::transaction(function () use ($request, $vendor) {
                return $this->saveFromRequest($request, $vendor);
            });
        } catch (QueryException $e) {
            $this->handleDuplicateEntry($e);
        }

        return response()->json([
            'gotourl' => route('tab.admin.vendor.details', [
                'vendor_id' => $response->vendor_id,
                'tab_name' => $tab_name,
            ])
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request, $id = null)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:10|unique:vendors,phone,' . $id . ',vendor_id',
            'email' => 'nullable|email:rfc,dns|unique:vendors,email,' . $id . ',vendor_id',
            'address' => 'nullable|string|max:500',
            'gst_number' => 'nullable|string|max:15|regex:/^[0-9A-Z]{15}$/i',
        ], [
            'name.required' => __('Vendor name is required.'),
            'name.max' => __('Vendor name cannot exceed 255 characters.'),
            'phone.required' => __('Phone number is required.'),
            'phone.digits' => __('Phone number must be exactly 10 digits.'),
            'phone.unique' => __('This phone number is already registered to another vendor.'),
            'email.email' => __('Please enter a valid email address.'),
            'email.unique' => __('This email address is already registered to another vendor.'),
            'address.max' => __('Address cannot exceed 500 characters.'),
            'gst_number.max' => __('GST number cannot exceed 15 characters.'),
            'gst_number.regex' => __('Please enter a valid 15-character GST number.'),
        ]);
    }

    private function saveFromRequest(Request $request, Vendor $vendor)
    {
        $isNew = ! $vendor->exists;

        $vendor->name = $request->name;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->address = $request->address;
        $vendor->gst_number = $request->filled('gst_number')
            ? strtoupper(trim($request->gst_number))
            : null;
        $vendor->is_active = (int) $request->is_active;
        $vendor->save();

        if ($isNew) {
            app(AccountingService::class)->getOrCreateVendorLedger($vendor->vendor_id);
        }

        voco_flash()->success(__('Vendor details updated!'));

        return $vendor;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }

    private function handleDuplicateEntry(QueryException $e): void
    {
        $message = $e->getMessage();

        if (str_contains($message, 'vendors_email_unique')) {
            throw ValidationException::withMessages([
                'email' => [__('This email address is already registered to another vendor.')],
            ]);
        }

        if (str_contains($message, 'vendors_phone_unique')) {
            throw ValidationException::withMessages([
                'phone' => [__('This phone number is already registered to another vendor.')],
            ]);
        }

        throw $e;
    }
}
