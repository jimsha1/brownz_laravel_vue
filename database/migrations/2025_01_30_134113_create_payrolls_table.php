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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id('payroll_id');
            $table->unsignedBigInteger('emp_id');
            $table->date('date')->nullable();
            $table->string('month')->nullable();
            $table->integer('year')->nullable();
            $table->string('emp_name')->nullable();
            $table->integer('emp_account_no')->nullable();
            $table->string('payment_mode')->nullable();
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->decimal('HRA_percentage', 5, 2)->nullable();
            $table->decimal('HRA_amount', 5, 2)->nullable();
            $table->decimal('TA_percentage', 5, 2)->nullable();
            $table->decimal('TA_amount', 5, 2)->nullable();
            $table->decimal('DA_percentage', 5, 2)->nullable();
            $table->decimal('DA_amount', 5, 2)->nullable();
            $table->decimal('special_allowance', 5, 2)->nullable();
            $table->decimal('professional_tax_percentage', 5, 2)->nullable();
            $table->decimal('professional_tax_amount', 5, 2)->nullable();
            $table->decimal('TDS_percentage', 5, 2)->nullable();
            $table->decimal('TDS_amount', 5, 2)->nullable();
            $table->decimal('net_salary', 10, 2)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
