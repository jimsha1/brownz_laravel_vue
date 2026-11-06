<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * PVA013 already contains dolma, anuma and swathy food money.
     * Remove the older separate vouchers for those same payments.
     */
    public function up(): void
    {
        $targets = [
            [
                'voucher_no' => 'PVA014',
                'description' => 'dolma food money',
                'pva013_remark' => 'dolma food money',
            ],
            [
                'voucher_no' => 'PVA017',
                'description' => 'SWATHI FOOD MONEY',
                'pva013_remark' => 'swathy food money',
            ],
            [
                'voucher_no' => 'JVA211',
                'description' => 'anuma food money',
                'pva013_remark' => 'anuma food money',
            ],
        ];

        DB::transaction(function () use ($targets) {
            foreach ($targets as $target) {
                $this->removeDuplicateVoucher($target);
            }
        });
    }

    public function down(): void
    {
        // Deleted vouchers are not restored.
    }

    /**
     * @param  array{voucher_no: string, description: string, pva013_remark: string}  $target
     */
    private function removeDuplicateVoucher(array $target): void
    {
        $groups = DB::table('transaction_groups')
            ->where('voucher_no', $target['voucher_no'])
            ->where('is_audit_only', 0)
            ->lockForUpdate()
            ->get();

        if ($groups->isEmpty()) {
            return;
        }

        if ($groups->count() !== 1) {
            throw new RuntimeException($target['voucher_no'].' matched more than one voucher.');
        }

        $group = $groups->first();
        if (trim((string) $group->description) !== $target['description']) {
            throw new RuntimeException(
                $target['voucher_no'].' description is "'.$group->description.'", expected "'.$target['description'].'".'
            );
        }

        $keptOnPva013 = DB::table('transactions as t')
            ->join('transaction_groups as tg', 'tg.id', '=', 't.transaction_group_id')
            ->where('tg.voucher_no', 'PVA013')
            ->where('tg.is_audit_only', 0)
            ->whereRaw('LOWER(TRIM(t.remarks)) = ?', [strtolower($target['pva013_remark'])])
            ->exists();

        if (! $keptOnPva013) {
            throw new RuntimeException('PVA013 does not contain '.$target['pva013_remark'].'.');
        }

        foreach ([
            'inventory_transactions',
            'vendor_payments',
            'commission_payments',
            'incentive_payments',
        ] as $table) {
            $linked = DB::table($table)->where('transaction_group_id', $group->id)->exists();
            if ($linked) {
                throw new RuntimeException($target['voucher_no'].' is linked from '.$table.' and was not removed.');
            }
        }

        $lines = DB::table('transactions as t')
            ->join('journals as j', 'j.id', '=', 't.journal_id')
            ->join('accounts as a', 'a.account_id', '=', 'j.account_id')
            ->where('t.transaction_group_id', $group->id)
            ->select('j.account_id', 'a.name', 't.debit', 't.credit')
            ->get();

        $actual = $lines
            ->map(fn ($line) => $line->name.'|'.number_format((float) $line->debit, 2, '.', '').'|'.number_format((float) $line->credit, 2, '.', ''))
            ->sort()
            ->values()
            ->all();

        $expected = ['Cash|0.00|1500.00', 'food money|1500.00|0.00'];
        sort($expected);

        if ($actual !== $expected) {
            throw new RuntimeException(
                $target['voucher_no'].' is not a 1,500.00 food-money cash payment: '.json_encode($actual)
            );
        }

        foreach ($lines as $line) {
            $delta = round(-((float) $line->debit - (float) $line->credit), 2);

            $journal = DB::table('journals')->where('account_id', $line->account_id)->lockForUpdate()->first();
            $account = DB::table('accounts')->where('account_id', $line->account_id)->lockForUpdate()->first();

            if (! $journal || ! $account) {
                throw new RuntimeException('Missing journal or account for '.$line->name.'.');
            }

            DB::table('journals')
                ->where('id', $journal->id)
                ->update(['balance' => round((float) $journal->balance + $delta, 2)]);

            DB::table('accounts')
                ->where('account_id', $account->account_id)
                ->update(['balance' => round((float) $account->balance + $delta, 2)]);
        }

        DB::table('transaction_groups')->where('id', $group->id)->delete();
    }
};
