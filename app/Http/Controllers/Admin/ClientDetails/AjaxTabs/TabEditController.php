<?php

namespace App\Http\Controllers\Admin\ClientDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Models\Account;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TabEditController extends Controller
{
    public function index(Request $request, ClientRepository $client_repo, $client_id, $tab_name)
    {
        $client_info = $client_repo->getClient($client_id, ($client_id == 0));

        if (empty($client_info->user_status)) {
            $client_info->user_status = \App\Models\User::USER_STATUS_ACTIVE;
        }

        $client_info->button_cancel_url = route('tab.admin.client.list');
        $client_info->form_save_url = route('tab.admin.client.save', ['client_id' => $client_id, 'tab_name' => $tab_name]);

        $mobile_number = (string) ($client_info->mobile ?? '');
        $digits = preg_replace('/\D+/', '', $mobile_number);
        if (strlen($digits) > 10 && str_starts_with($digits, '91')) {
            $digits = substr($digits, -10);
        }
        $mobile_number = substr($digits, 0, 10);

        $details_edit_vue = [
            'client_id' => $client_id,
            'tab_name' => $tab_name,
            'client_info' => $client_info,
            'mobile' => $mobile_number,
        ];

        // return $details_edit_vue;
        return view('admin.client-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, ClientRepository $client_repo, $client_id, $tab_name)
    {
        $form_client_id = $request->client_id;

        if ($form_client_id != $client_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for edit, or create a new model for insert
        if ($request->client_id > 0) {
            $client = $client_repo->model()->findOrFail($request->client_id);
            $user = User::find($client->client_id);
        } else {
            $client = $client_repo->model();
            $user = new User;
        }

        // Validate local 10-digit number, then store/check uniqueness with +91 country code
        $request->validate([
            'name' => 'required',
            'mobile' => ['required', 'digits:10'],
            'client_type' => 'required',
            'user_status' => 'required|in:active,inactive,pending,blocked',
        ], [
            'mobile.digits' => 'The mobile number must be exactly 10 digits.',
        ]);

        $request->merge([
            'mobile' => '+91'.$request->mobile,
        ]);

        $uniqueMobile = 'unique:users,mobile';
        if ((int) $request->client_id > 0) {
            // client_id matches users.id; ignore current user on update
            $uniqueMobile .= ','.$request->client_id;
        }

        $request->validate([
            'mobile' => $uniqueMobile,
        ]);

        // Start database transaction
        $response = DB::transaction(function () use ($request, $client, $user) {
            return $this->saveFromRequest($request, $client, $user);
        });

        return response()->json([
            'gotourl' => route('tab.admin.client.details', [
                'client_id' => $response->client_id,
                'tab_name' => 'edit',
            ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function saveFromRequest(Request $request, Client $client, User $user)
    {
        $is_new_user = empty($user->id);

        // Update or assign user details
        $user->name = $request->name;
        $user->email = $request->email;
        $user->date_of_birth = voco_calendar_to_mysql_date($request->date_of_birth, null);
        $user->mobile = $request->mobile;
        $user->telephone = (string) $request->telephone;
        $user->address = $request->address;
        $user->city = (string) $request->city;
        $user->state = (string) $request->state;
        $user->zip = (string) $request->zip;
        $user->gender = $request->gender;
        $user->user_status = $request->user_status ?: \App\Models\User::USER_STATUS_ACTIVE;

        if ($is_new_user) {
            $user->username = mt_rand(1, 99999).mt_rand(11111, 99999);

            $user->password = '';
        }

        // Save user record
        $user->save();

        if ($is_new_user) {
            // Set default `username` for new user
            $user->username = 'user'.str_pad($user->id, 3, '0', STR_PAD_LEFT);
            $user->save();

            $client->client_id = $user->id;
        }

        $client->client_type = $request->client_type;
        $client->save();

        if ($is_new_user) {

            $account = new Account;
            $account->code = Account::generateNextCode('client');

            $account->name = $request->name;
            $account->type = 'asset';
            $account->category = 'Current Asset';
            $account->balance = 0;
            $account->is_active = 1;
            // $account->client_id = $client->id;

            $account->save();
        }
        voco_flash()->success(__('Client details updated!'));

        return $client;
    }
}
