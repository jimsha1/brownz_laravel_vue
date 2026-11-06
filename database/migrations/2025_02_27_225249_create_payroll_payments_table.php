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
        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id('payroll_payment_id');
            $table->unsignedBigInteger('payroll_id');
            $table->string('payable_amount')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('payment_amount')->nullable();
            $table->string('balance_amount')->nullable();
            $table->text('narrations')->nullable();
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
        Schema::dropIfExists('payroll_payments');
    }
};
