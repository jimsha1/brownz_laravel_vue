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
        Schema::create('stock_purchases', function (Blueprint $table) {
            $table->id('purchase_id');
            $table->unsignedBigInteger('vendor_id');
            $table->date('purchase_date')->nullable();
            $table->string('purchase_invoice_no')->nullable();
            $table->text('purchase_description')->nullable();
            $table->string('payment_method')->nullable();
             // activity users
             $table->unsignedBigInteger('created_by')->nullable();
             $table->unsignedBigInteger('updated_by')->nullable();
             $table->foreign('created_by')->references('id')->on('users');
             $table->foreign('updated_by')->references('id')->on('users');
             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_purchases');
    }
};
