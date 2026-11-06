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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('inventory_item_id')->nullable();
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('cascade');

            $table->unsignedBigInteger('transaction_group_id')->nullable();
            $table->foreign('transaction_group_id')->references('id')->on('transaction_groups')->onDelete('cascade');

            $table->enum('type', ['purchase', 'sale', 'purchase_return', 'sale_return', 'adjustment', 'opening_stock']);
            $table->integer('quantity'); // Positive for additions, negative for removals
            // $table->decimal('unit_cost', 15, 2); // Cost at the time of transaction
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->date('date');

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
        Schema::dropIfExists('inventory_transactions');
    }
};
