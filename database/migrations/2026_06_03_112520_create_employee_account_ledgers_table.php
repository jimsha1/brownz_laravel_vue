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
        Schema::create('employee_account_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('ledger_id');
            $table->string('ledger_type')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('emp_id')->on('employees')->onDelete('cascade');
            $table->foreign('ledger_id')->references('account_id')->on('accounts');
            $table->unique('employee_id');
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_account_ledgers');
    }
};
