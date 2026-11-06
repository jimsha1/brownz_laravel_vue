<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('transaction_group_id');
            $table->decimal('amount', 12, 2);
            $table->date('payment_date')->nullable();
            $table->string('payment_mode', 30)->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('credit_payment_id')->nullable();
            $table->unsignedBigInteger('settlement_payment_id')->nullable();
            $table->timestamps();

            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->onDelete('cascade');
            $table->foreign('transaction_group_id')->references('id')->on('transaction_groups')->onDelete('cascade');
            $table->index(['transaction_group_id']);
            $table->index(['vendor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_payments');
    }
};
