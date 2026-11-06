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
        Schema::create('stock_purchase_item_tracks', function (Blueprint $table) {
            $table->id('purchase_item_track_id');
            $table->unsignedBigInteger('purchase_track_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->unsigned()->nullable();
            $table->decimal('tax_amount', 5, 2)->unsigned()->nullable();
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('purchase_total', 10, 2)->unsigned()->default(0);

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
        Schema::dropIfExists('stock_purchase_item_tracks');
    }
};
