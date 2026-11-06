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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            // $table->string('name');
            // $table->string('sku')->unique()->nullable();

            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('item_id')->on('stock_items');

            // $table->decimal('unit_cost', 15, 2); // Average cost per unit
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->integer('quantity')->default(0); // Current stock
            $table->enum('costing_method', ['weighted_average', 'fifo'])->default('weighted_average');
            $table->string('reason_code')->nullable();

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
        Schema::dropIfExists('inventory_items');
    }
};
