<?php

namespace App\Http\Controllers\Stock\StockOpenBalanceDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\StockOpenBalance;
use App\Models\TransactionGroup;
use App\Repositories\StockOpenBalanceRepository;
use App\Service\AccountingService;
use App\Service\EmployeeService;

class TabEditController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request, StockOpenBalanceRepository $stock_open_balance_repo, $open_balance_id, $tab_name)
    {
        $this->authorizeOpeningBalance($open_balance_id);

        $stock_open_balance_info = $this->findById($open_balance_id, $stock_open_balance_repo);

        $details_edit_vue = [
            'open_balance_id' => $open_balance_id,
            'tab_name' => $tab_name,
            'stock_open_balance_info' => $stock_open_balance_info,
            'urls' => [
                'button_cancel_url' => route('tab.stock.open.balance.list'),
                'form_save_url' => route('tab.stock.open.balance.save', ['open_balance_id' => $open_balance_id, 'tab_name' => $tab_name]),

            ],
            'dropdowns' => [
                'accounts' => \Facades\App\Models\Account::accountsDropdownJson(),
            ],
        ];

        return view('stock.stock-open-balance-details.ajax-tabs.tab-edit', compact('details_edit_vue'));
    }
    public function save(Request $request, StockOpenBalanceRepository $stock_open_balance_repo, EmployeeService $employeeService, $open_balance_id, $tab_name)
    {

        $this->authorizeOpeningBalance($open_balance_id);

        if ($request->open_balance_id != $open_balance_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        // Fetch db record for update, or create a new model for insert
        $stock_open_balance = $this->findById($open_balance_id, $stock_open_balance_repo);

        // Apply validation
        $this->applyValidation($request);

        // Editing an existing open balance requires the current user's password
        if ($stock_open_balance->open_balance_id) {
            $request->validate([
                'auth_password' => 'required|string',
            ], [
                'auth_password.required' => __('Password is required to update this opening balance.'),
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
        \DB::transaction(function () use ($request, $stock_open_balance) {
            return $this->saveFromRequest($request, $stock_open_balance);
        });

        return response()->json([
            'gotourl' => route('tab.stock.open.balance.list')
        ]);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function applyValidation(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
            'amount' => 'required',
            'opening_date' => 'required|date',
        ]);
    }

    private function saveFromRequest(Request $request, StockOpenBalance $stock_open_balance)
    {
        $account = Account::where('account_id', $request->account_id)->first();
        if (! $account) {
            throw new \Exception(__('Account not found.'));
        }

        $date = $request->opening_date;
        $group = null;

        if ($stock_open_balance->exists && $stock_open_balance->transaction_group_id) {
            $group = TransactionGroup::find($stock_open_balance->transaction_group_id);
        }

        if ($group) {
            $this->accountingService->updateOpeningBalance(
                $group->id,
                $account,
                $request->amount,
                'Opening Balance',
                $date
            );
        } else {
            $group = $this->accountingService->recordOpeningBalance(
                $account,
                $request->amount,
                'Opening Balance',
                $date
            );
            $stock_open_balance->transaction_group_id = $group->id;
        }

        $stock_open_balance->account_id = $request->account_id;
        $stock_open_balance->amount = $request->amount;
        $stock_open_balance->opening_date = $request->opening_date;

        $stock_open_balance->save();

        voco_flash()->success(__('Opening balance details updated!'));

        return $stock_open_balance;
    }

    private function authorizeOpeningBalance($open_balance_id): void
    {
        $permission = ((int) $open_balance_id) > 0
            ? 'opening_balance_edit'
            : 'opening_balance_create';

        if (! auth()->user()->can($permission)) {
            abort(403, __('You do not have permission to update this opening balance.'));
        }
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0)
            ? $repo->model()->with(['account'])->findOrFail($id)
            : $repo->model();
    }
}
