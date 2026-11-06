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
        Schema::create('employee_salary_advances', function (Blueprint $table) {
            $table->id('emp_salary_advance_id');
            $table->date('voucher_date');
            $table->unsignedBigInteger('emp_id');
            $table->double('advance_amount')->nullable();
            $table->enum('payment_mode', ['bank', 'cash'])->default('bank');
            $table->text('narrations')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('employee_salary_advances');
    }
};
