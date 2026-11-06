<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Track separate commission/incentive payments (not part of salary payroll).
     */
    public function up(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (! Schema::hasColumn('commissions', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('commission_amount');
            }
            if (! Schema::hasColumn('commissions', 'payment_status')) {
                $table->string('payment_status', 30)->default('unpaid')->after('paid_amount');
            }
            if (! Schema::hasColumn('commissions', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('payment_status');
            }
            if (! Schema::hasColumn('commissions', 'payment_mode')) {
                $table->string('payment_mode', 30)->nullable()->after('payment_date');
            }
            if (! Schema::hasColumn('commissions', 'payment_transaction_group_id')) {
                $table->unsignedBigInteger('payment_transaction_group_id')->nullable()->after('payment_mode');
            }
        });

        Schema::table('incentives', function (Blueprint $table) {
            if (! Schema::hasColumn('incentives', 'paid_amount')) {
                $table->decimal('paid_amount', 10, 2)->default(0)->after('incentive_amount');
            }
            if (! Schema::hasColumn('incentives', 'payment_status')) {
                $table->string('payment_status', 30)->default('unpaid')->after('paid_amount');
            }
            if (! Schema::hasColumn('incentives', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('payment_status');
            }
            if (! Schema::hasColumn('incentives', 'payment_mode')) {
                $table->string('payment_mode', 30)->nullable()->after('payment_date');
            }
            if (! Schema::hasColumn('incentives', 'payment_transaction_group_id')) {
                $table->unsignedBigInteger('payment_transaction_group_id')->nullable()->after('payment_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            foreach (['paid_amount', 'payment_status', 'payment_date', 'payment_mode', 'payment_transaction_group_id'] as $column) {
                if (Schema::hasColumn('commissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('incentives', function (Blueprint $table) {
            foreach (['paid_amount', 'payment_status', 'payment_date', 'payment_mode', 'payment_transaction_group_id'] as $column) {
                if (Schema::hasColumn('incentives', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
