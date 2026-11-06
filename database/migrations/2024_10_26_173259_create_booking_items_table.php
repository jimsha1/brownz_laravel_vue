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
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id('booking_item_id');

            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('price_id');

            $table->string('item_code', 50);
            $table->string('item_name', 255);
            $table->text('item_name_ar')->nullable();
            $table->decimal('unit_price', 10, 2)->unsigned();
            $table->integer('quantity')->unsigned();

            $table->decimal('discount_percent', 5, 2)->unsigned()->nullable();
            $table->decimal('discount_amount', 10, 2)->unsigned()->default(0.00);

            $table->decimal('tax_percent', 5, 2)->unsigned()->nullable();
            $table->decimal('tax_amount', 10, 2)->unsigned()->default(0.00);

            $table->decimal('line_total', 10, 2)->unsigned();

            $table->timestamps();

            // Activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->foreign('booking_id')->references('booking_id')->on('bookings');
            $table->foreign('item_id')->references('item_id')->on('stock_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
