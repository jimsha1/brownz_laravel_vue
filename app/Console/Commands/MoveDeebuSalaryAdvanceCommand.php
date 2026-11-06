<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Employee;
use App\Models\EmployeeAccountLedger;
use App\Models\Journal;
use App\Models\Payroll;
use App\Models\PayrollPayments;
use App\Models\Transaction;
use App\Models\TransactionGroup;
use App\Service\AccountingService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MoveDeebuSalaryAdvanceCommand extends Command
{
    protected $signature = 'payroll:move-deebu-advance
                            {--payroll= : Payroll id to adjust when more than one unpaid payroll has no advance}
                            {--execute : Apply the move. Without this flag the command only prints the plan}';

    protected $description = 'Move Deepu salary-advance vouchers onto Deebu salary advance and recover that amount on her unpaid payroll.';

    public function handle(AccountingService $accountingService): int
    {
        try {
            $plan = $this->buildPlan($this->option('payroll'));
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($plan === null) {
            $this->info('Deepu salary advance has no remaining balance. Nothing to move.');

            return self::SUCCESS;
        }

        $this->line('Employee #'.$plan['employee']->emp_id.' '.$plan['employee']->name);
        $this->line('From '.$this->accountLabel($plan['wrong']).' balance '.$this->money($plan['wrong']->balance));
        $this->line('To '.$this->accountLabel($plan['right']).' balance '.$this->money($plan['right']->balance));
        $this->newLine();
        $this->table(
            ['Voucher', 'Date', 'Debit', 'Credit', 'Narration'],
            $plan['lines']->map(function (Transaction $line) use ($plan) {
                $group = $plan['groups']->get($line->transaction_group_id);

                return [
                    $group->voucher_no ?? $line->transaction_group_id,
                    $line->date,
                    $this->money($line->debit),
                    $this->money($line->credit),
                    $group->description ?? '',
                ];
            })->all()
        );

        $payroll = $plan['payroll'];
        $advance = $plan['advance'];
        $payable = round((float) $payroll->payable_amount - $advance, 2);
        $this->newLine();
        $this->line(sprintf(
            'Payroll #%d %s %s: advance %s → %s, payable %s → %s',
            $payroll->payroll_id,
            $payroll->month,
            $payroll->year,
            $this->money($payroll->advance),
            $this->money($advance),
            $this->money($payroll->payable_amount),
            $this->money($payable)
        ));
        $this->line('A payroll advance-recovery journal will be posted on '.$payroll->voucher_date.'.');
        $this->line('Deepu salary advance will be set to zero and marked inactive.');

        if (! $this->option('execute')) {
            $this->warn('Dry run only. Re-run with --execute after checking this plan.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($accountingService, $plan, $advance, $payable) {
            $this->assertPlanUnchanged($plan);

            $rightJournal = Journal::query()
                ->where('account_id', $plan['right']->account_id)
                ->lockForUpdate()
                ->first();
            if (! $rightJournal) {
                $rightJournal = Journal::create([
                    'account_id' => $plan['right']->account_id,
                    'balance' => 0,
                ]);
            }

            foreach ($plan['lines'] as $line) {
                $locked = Transaction::query()->where('id', $line->id)->lockForUpdate()->first();
                $locked->journal_id = $rightJournal->id;
                $locked->remarks = $this->renameNarration($locked->remarks);
                $locked->save();
            }

            foreach ($plan['groups'] as $group) {
                $lockedGroup = TransactionGroup::query()->where('id', $group->id)->lockForUpdate()->first();
                $lockedGroup->description = $this->renameNarration($lockedGroup->description);
                $lockedGroup->save();

                Transaction::query()
                    ->where('transaction_group_id', $group->id)
                    ->get()
                    ->each(function (Transaction $other) {
                        if ($other->remarks) {
                            $other->remarks = $this->renameNarration($other->remarks);
                            $other->save();
                        }
                    });
            }

            $this->rebuildAccount($plan['wrong']->account_id, true);
            $this->rebuildAccount($plan['right']->account_id, false);

            $payroll = Payroll::query()->where('payroll_id', $plan['payroll']->payroll_id)->lockForUpdate()->first();
            $payroll->advance = $advance;
            $payroll->payable_amount = $payable;
            $payroll->is_scheduled = $payable <= 0 ? 1 : 0;
            $payroll->save();

            $accountingService->recordMultiEntryTransaction([
                [
                    'account_id' => $plan['salaryPayableId'],
                    'debit' => $advance,
                    'credit' => 0,
                ],
                [
                    'account_id' => $plan['right']->account_id,
                    'debit' => 0,
                    'credit' => $advance,
                ],
            ], 'Advance adjustment on payroll for employee #'.$plan['employee']->emp_id, $payroll->voucher_date);
        });

        $this->info('Deebu salary advance and payroll were updated.');

        return self::SUCCESS;
    }

    /**
     * @return array{
     *     employee: Employee,
     *     wrong: Account,
     *     right: Account,
     *     salaryPayableId: int,
     *     lines: Collection<int, Transaction>,
     *     groups: Collection<int, TransactionGroup>,
     *     payroll: Payroll,
     *     advance: float
     * }|null
     */
    private function buildPlan($payrollOption): ?array
    {
        $employees = Employee::query()
            ->with('user')
            ->get()
            ->filter(function (Employee $employee) {
                $name = $this->normalize((string) ($employee->name ?: optional($employee->user)->name));

                return $name === 'deebu';
            })
            ->values();

        if ($employees->count() !== 1) {
            throw new \RuntimeException('Expected one employee named deebu, found '.$employees->count().'.');
        }

        $employee = $employees->first();
        $accounts = Account::query()->where('type', 'asset')->get();
        $wrongAccounts = $accounts->filter(fn (Account $account) => $this->normalize($account->name) === 'deepu salary advance')->values();
        $rightAccounts = $accounts->filter(fn (Account $account) => $this->normalize($account->name) === 'deebu salary advance')->values();

        if ($wrongAccounts->count() !== 1) {
            throw new \RuntimeException('Expected one Deepu salary advance ledger, found '.$wrongAccounts->count().'.');
        }
        if ($rightAccounts->count() !== 1) {
            throw new \RuntimeException('Expected one Deebu salary advance ledger, found '.$rightAccounts->count().'.');
        }

        $wrong = $wrongAccounts->first();
        $right = $rightAccounts->first();
        $advanceLink = EmployeeAccountLedger::query()
            ->where('employee_id', $employee->emp_id)
            ->where('ledger_type', 'advance_salary')
            ->first();

        if (! $advanceLink || (int) $advanceLink->ledger_id !== (int) $right->account_id) {
            throw new \RuntimeException('Deebu is not linked to the deebu SALARY ADVANCE ledger.');
        }

        $salaryLink = EmployeeAccountLedger::query()
            ->where('employee_id', $employee->emp_id)
            ->where('ledger_type', 'salary')
            ->first();
        if (! $salaryLink) {
            throw new \RuntimeException('Deebu has no salary payable ledger.');
        }

        if (Schema::hasTable('financial_year_opening_balances')
            && DB::table('financial_year_opening_balances')->where('account_id', $wrong->account_id)->exists()) {
            throw new \RuntimeException('Deepu salary advance has a financial-year opening balance. This command will not move it.');
        }

        $wrongJournal = Journal::query()->where('account_id', $wrong->account_id)->first();
        $lines = $wrongJournal
            ? Transaction::query()->where('journal_id', $wrongJournal->id)->orderBy('date')->orderBy('id')->get()
            : collect();

        if ($lines->isEmpty() || abs((float) $wrong->balance) < 0.01) {
            return null;
        }

        $advance = round((float) $lines->sum('debit') - (float) $lines->sum('credit'), 2);
        if ($advance <= 0 || abs($advance - round((float) $wrong->balance, 2)) >= 0.01) {
            throw new \RuntimeException('Deepu ledger balance does not match its voucher lines.');
        }
        if (round((float) $right->balance, 2) !== 0.0) {
            throw new \RuntimeException('Deebu salary advance already has a balance of '.$this->money($right->balance).'. Refusing to merge into it.');
        }

        $groups = TransactionGroup::query()
            ->whereIn('id', $lines->pluck('transaction_group_id')->unique()->all())
            ->get()
            ->keyBy('id');

        $payrolls = Payroll::query()
            ->where('emp_id', $employee->emp_id)
            ->orderBy('payroll_id')
            ->get()
            ->filter(function (Payroll $payroll) use ($advance) {
                return round((float) $payroll->advance, 2) === 0.0
                    && round((float) $payroll->payable_amount, 2) + 0.0001 >= $advance
                    && ! PayrollPayments::query()->where('payroll_id', $payroll->payroll_id)->exists();
            })
            ->values();

        if ($payrollOption !== null && $payrollOption !== '') {
            $payroll = $payrolls->firstWhere('payroll_id', (int) $payrollOption);
            if (! $payroll) {
                throw new \RuntimeException('Payroll #'.$payrollOption.' is not an unpaid Deebu payroll with zero advance.');
            }
        } elseif ($payrolls->count() !== 1) {
            $list = $payrolls->map(fn (Payroll $payroll) => '#'.$payroll->payroll_id.' '.$payroll->month.' '.$payroll->year)->implode(', ');
            throw new \RuntimeException(
                'Expected one unpaid Deebu payroll with zero advance, found '.$payrolls->count().($list !== '' ? ' ('.$list.')' : '').'. Pass --payroll=ID.'
            );
        } else {
            $payroll = $payrolls->first();
        }

        return [
            'employee' => $employee,
            'wrong' => $wrong,
            'right' => $right,
            'salaryPayableId' => (int) $salaryLink->ledger_id,
            'lines' => $lines,
            'groups' => $groups,
            'payroll' => $payroll,
            'advance' => $advance,
        ];
    }

    private function assertPlanUnchanged(array $plan): void
    {
        $wrong = Account::query()->where('account_id', $plan['wrong']->account_id)->lockForUpdate()->first();
        $right = Account::query()->where('account_id', $plan['right']->account_id)->lockForUpdate()->first();
        $payroll = Payroll::query()->where('payroll_id', $plan['payroll']->payroll_id)->lockForUpdate()->first();

        if (round((float) $wrong->balance, 2) !== round((float) $plan['wrong']->balance, 2)
            || round((float) $right->balance, 2) !== 0.0
            || round((float) $payroll->advance, 2) !== 0.0
            || round((float) $payroll->payable_amount, 2) !== round((float) $plan['payroll']->payable_amount, 2)
            || PayrollPayments::query()->where('payroll_id', $payroll->payroll_id)->exists()) {
            throw new \RuntimeException('The ledgers or payroll changed while this command was running. Run it again.');
        }
    }

    private function rebuildAccount(int $accountId, bool $deactivateWhenZero): void
    {
        $journals = Journal::query()->where('account_id', $accountId)->lockForUpdate()->get();
        foreach ($journals as $journal) {
            $journal->balance = round((float) Transaction::query()
                ->where('journal_id', $journal->id)
                ->selectRaw('COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as balance')
                ->value('balance'), 2);
            $journal->save();
        }

        $account = Account::query()->where('account_id', $accountId)->lockForUpdate()->first();
        $account->balance = round((float) $journals->sum('balance'), 2);
        if ($deactivateWhenZero && abs((float) $account->balance) < 0.01) {
            $account->is_active = 0;
        }
        $account->save();
    }

    private function renameNarration(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return preg_replace('/deepu|deppu/i', 'deebu', $value);
    }

    private function normalize(string $value): string
    {
        $value = strtolower(trim($value));

        return preg_replace('/\s+/', ' ', $value) ?? $value;
    }

    private function accountLabel(Account $account): string
    {
        return $account->name.' ('.$account->code.', #'.$account->account_id.')';
    }

    private function money($amount): string
    {
        return number_format((float) $amount, 2, '.', '');
    }
}
