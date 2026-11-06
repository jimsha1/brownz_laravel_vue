<?php

namespace App\Http\Controllers\Salon\BeauticianDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AutoIncrementMaster;
use App\Repositories\BeauticianRepository;
use App\Models\Beautician;
use App\Models\BeauticianService;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TabEditController extends Controller
{
    public function index(Request $request, BeauticianRepository $beautician_repo, $beautician_id, $tab_name)
    {

        $beautician_info = $beautician_repo->getBeautician($beautician_id, ($beautician_id == 0));

        $beautician_info->photo_src_url = beautician_photo_url($beautician_info->profile_photo_path ?? null, 150, 150, 1);
        if ($beautician_id > 0) {
            $beautician_info->service_id = BeauticianService::where('beautician_id', $beautician_id)
                ->pluck('service_id')
                ->first();
        }

        $details_edit_vue = [
            'beautician_id' => $beautician_id,
            'member_id' => '',
            'tab_name' => $tab_name,
            'beautician_info' => $beautician_info,
            'urls' => [
                'button_cancel_url' => route('tab.salon.beautician.list'),
                'form_save_url' => route('tab.salon.beautician.save', ['beautician_id' => $beautician_id, 'tab_name' => $tab_name]),
                'form_update_photo_url' => route('admin.beautician.photo.save', ['beautician_id' => $beautician_id]),
            ],
            'dropdowns' => [
                'services' => \Facades\App\Models\Service::servicesDropdownJson(),
                'shift' => Shift::where('is_active', 1)->get(),
            ],
        ];

        return view('salon.beautician-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, BeauticianRepository $beautician_repo, $beautician_id, $tab_name)
    {

        if ($request->beautician_id != $beautician_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        if ($request->beautician_id > 0) {
            $beautician = $beautician_repo->model()->findOrFail($request->beautician_id);
            $user = User::find($beautician->beautician_id);
        } else {
            $beautician = $beautician_repo->model();
            $user = new User();
        }

        // Apply validation
        $this->applyValidation($request, $user);

        // Start database transaction
        $response = DB::transaction(function () use ($request, $beautician, $user) {
            return $this->saveFromRequest($request, $beautician, $user);
        });

        return response()->json([
            'gotourl' => route('tab.salon.beautician.details', [
                'beautician_id' => $response->beautician_id,
                'tab_name' => $tab_name,
            ])
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request, User $user)
    {
        $rules = [
            'name' => 'required',
            'mobile' => 'required',
            // 'gender' => 'required',
            'join_date' => 'required',
            // 'service_id' => 'required',
            'shift_id' => 'required',
        ];

        if (empty($user->id)) {
            // Create mode
            $rules['username'] = 'required';
            $rules['password'] = 'required';
        } else {
            // Edit mode
            // $rules['short_bio_en'] = 'required';
            // $rules['long_bio_en'] = 'required';
        }

        $request->validate($rules);
    }

    public function generateEmployeeCode()
    {
        $auto_emp_code = AutoIncrementMaster::where('auto_code', 'AI_EMPLOYEE_CODE')->first();
        $emp_code = $auto_emp_code->auto_increment_general_value;
        $auto_emp_code->auto_increment_general_value = $emp_code + 1;
        $auto_emp_code->save();
        return  $auto_emp_code->auto_prefix . $emp_code;
    }
    private function saveFromRequest(Request $request, Beautician $beautician, User $user)
    {
        $is_new_user = empty($user->id);

        $user->name = $request->name;
        // $user->name_arabic = $request->name_arabic;
        $user->mobile = $request->mobile;
        $user->gender = $request->gender;
        $user->shift_id = $request->shift_id;
        $user->email = (string) $request->email;
        $user->telephone = (string) $request->telephone;

        // Create mode
        if ($is_new_user) {
            $user->username = $request->username;
            $user->password = bcrypt($request->password);
            $user->user_status = \App\Models\User::USER_STATUS_ACTIVE;
        }

        // Save user
        $user->save();

        if ($is_new_user) {
            // Create mode
            $beautician->beautician_id = $user->id;

            /* Allow access to current outlet */
            $user_outlet = new \App\Models\UserHasOutlet();
            $user_outlet->outlet_id = session_outlet_id();
            $user_outlet->user_id = $user->id;
            $user_outlet->created_at = new \DateTime();
            $user_outlet->created_by = Auth::id();
            $user_outlet->save();

            // Insert new entry in employees table
            $employee = new \App\Models\Employee();
            $employee->user_id = $user->id;
            $employee->emp_code = $this->generateEmployeeCode();
            $employee->name = $request->name;
            $employee->designation = 'emp';
            $employee->join_date = voco_calendar_to_mysql_date($request->join_date, null);
            $employee->status = 'active';
            $employee->created_by = Auth::id();
            $employee->save();

        } else {
            // Edit mode
            $beautician->short_bio_en = $request->short_bio_en;
            $beautician->long_bio_en = $request->long_bio_en;
        }

        // Save beautician
        $beautician->join_date = voco_calendar_to_mysql_date($request->join_date, null);
        $beautician->save();

        // Role sync only for new users, and only if a matching role exists
        if ($is_new_user) {
            $this->assignBeauticianRoleIfAvailable($user);
        }

        voco_flash()->success(__('Beautician details updated!'));

        return $beautician;
    }

    /**
     * Assign a beautician/stylist role when creating a user, if configured.
     */
    private function assignBeauticianRoleIfAvailable(User $user): void
    {
        $candidates = ['Stylist', 'Hairstylist', 'Hairstylist Unisex', 'beautition', 'Beautician'];

        $roleName = \Spatie\Permission\Models\Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $candidates)
            ->orderByRaw("FIELD(name, '".implode("','", $candidates)."')")
            ->value('name');

        if ($roleName) {
            $user->syncRoles([$roleName]);
        }
    }
}
