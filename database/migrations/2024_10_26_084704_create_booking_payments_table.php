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
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id('payment_id');

            // @TODO - remove `sales_order_id`
            $table->unsignedBigInteger('sales_order_id')->nullable();

            $table->unsignedBigInteger('booking_id')->nullable();

            $table->decimal('amount_taken', 10, 2);
            $table->decimal('amount_given', 10, 2);

            $table->enum('payment_mode', ['cash', 'card']);
            $table->enum('card_type', ['master', 'visa'])->nullable();

            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_payments');
    }
};
