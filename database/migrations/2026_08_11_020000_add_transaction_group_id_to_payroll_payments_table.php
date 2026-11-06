<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_group_id')->nullable()->after('payroll_id');
            $table->foreign('transaction_group_id')
                ->references('id')
                ->on('transaction_groups')
                ->nullOnDelete();
            $table->index(['transaction_group_id']);
        });
    }

    public function down(): void
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->dropForeign(['transaction_group_id']);
            $table->dropColumn('transaction_group_id');
        });
    }
};
