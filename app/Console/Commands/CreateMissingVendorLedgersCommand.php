<?php

namespace App\Console\Commands;

use App\Models\Vendor;
use App\Service\AccountingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateMissingVendorLedgersCommand extends Command
{
    protected $signature = 'vendors:create-missing-ledgers
                            {--execute : Create the ledgers. Without this flag the command only lists vendors that need one}';

    protected $description = 'Create a liability ledger for each vendor that does not have one yet.';

    public function handle(AccountingService $accountingService): int
    {
        $execute = (bool) $this->option('execute');

        $vendors = Vendor::query()
            ->where(function ($query) {
                $query->whereNull('ledger_id')->orWhere('ledger_id', 0);
            })
            ->orderBy('vendor_id')
            ->get(['vendor_id', 'name', 'balance']);

        if ($vendors->isEmpty()) {
            $this->info('Every vendor already has a ledger.');

            return self::SUCCESS;
        }

        $this->info($vendors->count().' vendor(s) have no ledger.');
        $this->table(
            ['Vendor ID', 'Name', 'Outstanding'],
            $vendors->map(fn (Vendor $vendor) => [
                $vendor->vendor_id,
                $vendor->name,
                number_format((float) $vendor->balance, 2),
            ])->all()
        );

        if (! $execute) {
            $this->comment('Nothing was changed. Run with --execute to create these ledgers.');

            return self::SUCCESS;
        }

        $created = 0;

        DB::transaction(function () use ($vendors, $accountingService, &$created) {
            foreach ($vendors as $vendor) {
                $ledgerId = $accountingService->getOrCreateVendorLedger($vendor->vendor_id);
                $this->line("Vendor #{$vendor->vendor_id} {$vendor->name} → ledger #{$ledgerId}");
                $created++;
            }
        });

        $this->info("Created {$created} vendor ledger(s).");

        return self::SUCCESS;
    }
}
