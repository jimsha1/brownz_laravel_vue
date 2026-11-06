<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link incentive/commission accruals to journal vouchers.
     */
    public function up(): void
    {
        Schema::table('incentives', function (Blueprint $table) {
            if (! Schema::hasColumn('incentives', 'transaction_group_id')) {
                $table->unsignedBigInteger('transaction_group_id')->nullable()->after('incentive_status');
            }
        });

        Schema::table('commissions', function (Blueprint $table) {
            if (! Schema::hasColumn('commissions', 'transaction_group_id')) {
                $table->unsignedBigInteger('transaction_group_id')->nullable()->after('commission_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incentives', function (Blueprint $table) {
            if (Schema::hasColumn('incentives', 'transaction_group_id')) {
                $table->dropColumn('transaction_group_id');
            }
        });

        Schema::table('commissions', function (Blueprint $table) {
            if (Schema::hasColumn('commissions', 'transaction_group_id')) {
                $table->dropColumn('transaction_group_id');
            }
        });
    }
};
