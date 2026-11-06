<?php

use App\Service\AccountingService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('stock_open_balances', 'transaction_group_id')) {
            Schema::table('stock_open_balances', function (Blueprint $table) {
                $table->unsignedBigInteger('transaction_group_id')->nullable()->after('opening_date');
                $table->foreign('transaction_group_id')
                    ->references('id')
                    ->on('transaction_groups')
                    ->nullOnDelete();
            });
        }

        app(AccountingService::class)->consolidateOpeningBalanceLedgers();
    }

    public function down(): void
    {
        if (! Schema::hasColumn('stock_open_balances', 'transaction_group_id')) {
            return;
        }

        Schema::table('stock_open_balances', function (Blueprint $table) {
            $table->dropForeign(['transaction_group_id']);
            $table->dropColumn('transaction_group_id');
        });
    }
};
