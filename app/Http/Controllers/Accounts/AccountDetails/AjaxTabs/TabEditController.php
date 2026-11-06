<?php

namespace App\Http\Controllers\Accounts\AccountDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AccountRepository;
use App\Models\Account;
use App\Models\AccountCategory;
use App\Service\EmployeeService;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TabEditController extends Controller
{
    public function index(Request $request, AccountRepository $account_repo, $account_id, $tab_name)
    {
        $account_info = $this->findById($account_id, $account_repo);

        $is_system_defined = $account_info->exists && (int) $account_info->is_system_defined === 1;
        if ($account_info->exists) {
            $account_info->is_active = (int) $account_info->is_active;
        }
        $account_info->category = (string) ($account_info->category ?? '');

        $details_edit_vue = [
            'account_id' => $account_id,
            'tab_name' => $tab_name,
            'account_info' => $account_info,
            'is_system_defined' => $is_system_defined,
            'categories' => AccountCategory::query()
                ->orderBy('name')
                ->get(['category_id', 'name', 'type', 'is_system'])
                ->map(function ($row) {
                    return [
                        'category_id' => (int) $row->category_id,
                        'name' => $row->name,
                        'type' => $row->type,
                        'is_system' => $row->is_system ? 1 : 0,
                    ];
                })
                ->values(),
            'urls' => [
                'button_cancel_url' => route('tab.accounts.account.list'),
                'form_save_url' => route('tab.accounts.account.save', ['account_id' => $account_id, 'tab_name' => $tab_name]),
                'category_store_url' => route('accounts.account-categories.store'),
                'category_delete_url' => url('accounts/account-categories'),
            ],
        ];

        return view('accounts.account-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }

    public function save(Request $request, AccountRepository $account_repo, EmployeeService $employeeService, $account_id, $tab_name)
    {
        if ($request->account_id != $account_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        $account = $this->findById($account_id, $account_repo);

        if ($account->exists && (int) $account->is_system_defined === 1) {
            throw new \Exception(__('System defined ledgers cannot be edited.'));
        }

        // Apply validation
        $this->applyValidation($request);

        // Editing an existing account requires the current user's password
        if ($account->exists) {
            $request->validate([
                'auth_password' => 'required|string',
            ], [
                'auth_password.required' => __('Password is required to update this account.'),
            ]);

            $passwordError = $employeeService->validateCurrentUserPassword($request->auth_password);
            if ($passwordError) {
                return response()->json([
                    'message' => $passwordError,
                    'js_notifications' => ['error' => $passwordError],
                    'errors' => ['auth_password' => [$passwordError]],
                ], 422);
            }
        }

        // Start database transaction
        $response = DB::transaction(function () use ($request, $account) {
            return $this->saveFromRequest($request, $account);
        });
        
        return response()->json([
            'gotourl' => route('tab.accounts.account.details', [
                'account_id' => $response->account_id,
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
            'name' => 'required',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'category' => [
                'nullable',
                'string',
                'max:100',
                Rule::exists('account_categories', 'name')->where(function ($query) use ($request) {
                    $query->where('type', $request->type);
                }),
            ],
        ]);
    }

    private function saveFromRequest(Request $request, Account $account)
    {
        // Auto-generate code if empty (new account)
        if (empty($account->code)) {
            $account->code = Account::generateNextCode($request->type);
        }
        $account->name = $request->name;
        $account->type = $request->type;
        $account->category = $request->filled('category')
            ? AccountCategory::normalizeName((string) $request->category)
            : null;
        // Ledger balance is maintained only by journal postings — never overwrite from this form.
        if (! $account->exists) {
            $account->balance = 0;
        }
        $account->is_active = (int) $request->is_active;
        $account->save();

        voco_flash()->success(__('Account details updated!'));

        return $account;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }
}
