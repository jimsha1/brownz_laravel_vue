<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\EmployeeAccountLedger;
use App\Service\AccountingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateMissingEmployeeLedgersCommand extends Command
{
    protected $signature = 'employees:create-missing-ledgers
                            {--execute : Create the ledgers. Without this flag the command only lists employees that need one}';

    protected $description = 'Create advance, salary, incentive, and commission ledgers for employees that are missing them.';

    private const LEDGER_TYPES = ['advance_salary', 'salary', 'incentive', 'commission'];

    public function handle(AccountingService $accountingService): int
    {
        $execute = (bool) $this->option('execute');

        $employees = Employee::query()
            ->with('user')
            ->orderBy('emp_id')
            ->get();

        $missing = $employees->map(function (Employee $employee) {
            $present = EmployeeAccountLedger::query()
                ->where('employee_id', $employee->emp_id)
                ->pluck('ledger_type')
                ->all();

            $needed = array_values(array_diff(self::LEDGER_TYPES, $present));

            return [
                'employee' => $employee,
                'needed' => $needed,
            ];
        })->filter(fn (array $row) => $row['needed'] !== [])->values();

        if ($missing->isEmpty()) {
            $this->info('Every employee already has advance, salary, incentive, and commission ledgers.');

            return self::SUCCESS;
        }

        $this->info($missing->count().' employee(s) are missing at least one ledger.');
        $this->table(
            ['Employee ID', 'Name', 'Status', 'Missing'],
            $missing->map(function (array $row) {
                $employee = $row['employee'];
                $name = trim((string) ($employee->name ?: optional($employee->user)->name));

                return [
                    $employee->emp_id,
                    $name !== '' ? $name : 'Employee '.$employee->emp_id,
                    $employee->status,
                    implode(', ', $row['needed']),
                ];
            })->all()
        );

        if (! $execute) {
            $this->comment('Nothing was changed. Run with --execute to create these ledgers.');

            return self::SUCCESS;
        }

        $createdEmployees = 0;
        $failed = 0;

        foreach ($missing as $row) {
            $employee = $row['employee'];
            $name = trim((string) ($employee->name ?: optional($employee->user)->name));
            $label = $name !== '' ? $name : 'Employee '.$employee->emp_id;

            try {
                $ledgers = DB::transaction(function () use ($accountingService, $employee) {
                    return $accountingService->ensureEmployeeLedgers($employee->emp_id);
                });

                $this->line("Employee #{$employee->emp_id} {$label} → ".json_encode($ledgers));
                $createdEmployees++;
            } catch (\Throwable $e) {
                $failed++;
                $this->error("Employee #{$employee->emp_id} {$label} failed: ".$e->getMessage());
            }
        }

        $this->info("Updated {$createdEmployees} employee(s).");
        if ($failed > 0) {
            $this->error("{$failed} employee(s) could not be updated.");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
