<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('advance', 10, 2)->nullable()->after('TDS_amount');
            $table->integer('total_leave')->nullable()->after('advance');
            $table->integer('approve_leave')->nullable()->after('total_leave');
            $table->decimal('deduction', 10, 2)->nullable()->after('approve_leave');
            $table->decimal('payable_amount', 10, 2)->nullable()->after('deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['advance', 'total_leave', 'approve_leave', 'deduction', 'payable_amount']);
        });
    }
};
