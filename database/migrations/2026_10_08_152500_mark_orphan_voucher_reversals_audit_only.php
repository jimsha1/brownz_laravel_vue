<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Voucher edits delete the original transaction group and post
     * "Reversal of Voucher Transaction #{id}". With the original gone, that
     * reversal has nothing to offset and inflates Cash (and the other side).
     * Mark those orphan reversals as audit-only so statements match the ledger.
     */
    public function up(): void
    {
        $groups = DB::table('transaction_groups')
            ->where('description', 'like', 'Reversal of Voucher Transaction #%')
            ->where('is_audit_only', 0)
            ->get(['id', 'description']);

        foreach ($groups as $group) {
            if (! preg_match('/Reversal of Voucher Transaction #(\d+)/i', (string) $group->description, $matches)) {
                continue;
            }

            $originalExists = DB::table('transaction_groups')
                ->where('id', (int) $matches[1])
                ->exists();

            if ($originalExists) {
                continue;
            }

            DB::table('transaction_groups')
                ->where('id', $group->id)
                ->update(['is_audit_only' => 1]);
        }
    }

    public function down(): void
    {
        $groups = DB::table('transaction_groups')
            ->where('description', 'like', 'Reversal of Voucher Transaction #%')
            ->where('is_audit_only', 1)
            ->get(['id', 'description']);

        foreach ($groups as $group) {
            if (! preg_match('/Reversal of Voucher Transaction #(\d+)/i', (string) $group->description, $matches)) {
                continue;
            }

            $originalExists = DB::table('transaction_groups')
                ->where('id', (int) $matches[1])
                ->exists();

            if ($originalExists) {
                continue;
            }

            DB::table('transaction_groups')
                ->where('id', $group->id)
                ->update(['is_audit_only' => 0]);
        }
    }
};
