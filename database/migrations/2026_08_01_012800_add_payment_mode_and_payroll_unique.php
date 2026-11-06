<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payroll_payments', 'payment_mode')) {
                $table->string('payment_mode', 20)->nullable()->after('narrations');
            }
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->unique(['emp_id', 'month', 'year'], 'payrolls_emp_month_year_unique');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_payments', 'payment_mode')) {
                $table->dropColumn('payment_mode');
            }
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropUnique('payrolls_emp_month_year_unique');
        });
    }
};
