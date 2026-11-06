<?php

namespace App\Http\Controllers\Expenses\ExpenseDetails\AjaxTabs;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Employee;
use App\Models\Expense;
use App\Repositories\ExpenseRepository;
use App\Service\AccountingService;
use App\Service\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TabEditController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request, ExpenseRepository $expense_repo, $expense_id, $tab_name)
    {
        $expense_info = $this->findById($expense_id, $expense_repo);
        if (! $expense_info->expense_id) {
            $expense_info->account_type = '';
            $expense_info->amount = null;
            $expense_info->date = now()->toDateString();
            $expense_info->short_note = '';
        } else {
            $expense_info->account_type = strtolower((string) $expense_info->account_type);
        }
        $account = Account::whereRaw('LOWER(type) = ?', ['expense'])
            ->orderBy('name')
            ->get(['account_id', 'name', 'code']);
        $employee = Employee::where('status', 'active')->get();
        $details_edit_vue = [
            'expense_id' => (int) $expense_id,
            'tab_name' => $tab_name,
            'expense_info' => $expense_info,
            'original_account_type' => $expense_info->expense_id ? strtolower((string) $expense_info->account_type) : '',
            'original_amount' => $expense_info->expense_id ? round((float) $expense_info->amount, 2) : 0,
            'cash_balance' => $this->accountingService->getCashOrBankBalance('cash'),
            'bank_balance' => $this->accountingService->getCashOrBankBalance('bank'),
            'urls' => [
                'button_cancel_url' => route('tab.expenses.expense.list'),
                'form_save_url' => route('tab.expenses.expense.save', ['expense_id' => $expense_id, 'tab_name' => $tab_name]),
            ],
            'accounts' => $account,
        ];

        return view('expenses.expense-details.ajax-tabs.tab-edit', compact('details_edit_vue', 'account', 'employee'));
    }

    public function save(Request $request, ExpenseRepository $expense_repo, EmployeeService $employeeService, $expense_id, $tab_name)
    {
        if ($request->expense_id != $expense_id) {
            throw new \Exception(__('You are redirected from an invalid link'));
        }

        $expense = $this->findById($expense_id, $expense_repo);
        $this->applyValidation($request);

        // Editing an existing expense requires the current user's password
        if ($expense->expense_id) {
            $request->validate([
                'auth_password' => 'required|string',
            ], [
                'auth_password.required' => __('Password is required to update this expense.'),
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

        DB::transaction(function () use ($request, $expense) {
            return $this->saveFromRequest($request, $expense);
        });

        return response()->json([
            'gotourl' => route('tab.expenses.expense.list'),
        ]);
    }

    private function applyValidation(Request $request)
    {
        $request->validate([
            'account_id' => [
                'required',
                Rule::exists('accounts', 'account_id')->where(function ($query) {
                    $query->whereRaw('LOWER(type) = ?', ['expense']);
                }),
            ],
            'account_type' => 'required|in:cash,bank',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'short_note' => 'nullable|string|max:500',
        ], [
            'account_id.exists' => __('Please select a valid expense account.'),
            'account_type.in' => __('Payment must be from Cash or Bank.'),
            'amount.min' => __('Expense amount must be greater than zero.'),
        ]);
    }

    private function saveFromRequest(Request $request, Expense $expense)
    {
        $amount = round((float) $request->amount, 2);
        $paymentMode = strtolower((string) $request->account_type);
        $date = $request->date;
        $note = $request->short_note ?: 'Expense';

        if ($expense->expense_id) {
            if ($expense->account_type === 'cash') {
                $this->accountingService->reverseCashExpense(
                    $expense->amount,
                    'Reversal: '.$expense->short_note,
                    $expense->date,
                    $expense->account_id
                );
            } else {
                $this->accountingService->reverseBankExpense(
                    $expense->amount,
                    'Reversal: '.$expense->short_note,
                    $expense->date,
                    $expense->account_id
                );
            }
        }

        // Validate after reversing old entry so edit can reuse the restored cash/bank balance
        $this->accountingService->validateAccountBalance($paymentMode, $amount);

        $expense->account_id = $request->account_id;
        $expense->account_type = $paymentMode;
        $expense->amount = $amount;
        $expense->date = $date;
        $expense->short_note = $note;
        $expense->save();

        if ($paymentMode === 'cash') {
            $this->accountingService->recordCashExpense(
                $amount,
                $note,
                $date,
                $request->account_id
            );
        } else {
            $this->accountingService->recordBankExpense(
                $amount,
                $note,
                $date,
                $request->account_id
            );
        }

        voco_flash()->success(__('Expense details updated!'));

        return $expense;
    }

    private function findById($id, \App\Repositories\Base\BaseRepository $repo)
    {
        return ($id > 0) ? $repo->model()->findOrFail($id) : $repo->model();
    }
}
