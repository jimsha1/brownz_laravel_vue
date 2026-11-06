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

        // ---------------------------------------------------------------------
        // stock_item_groups
        // ---------------------------------------------------------------------

        Schema::create('stock_item_groups', function (Blueprint $table) {
            $table->id('item_group_id');
            $table->string('group_name', 255);

            $table->timestamps();

            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_show_website')->unsigned()->default(1);
        });

        // ---------------------------------------------------------------------
        // stock_brands
        // ---------------------------------------------------------------------

        Schema::create('stock_brands', function (Blueprint $table) {
            $table->id('brand_id');
            $table->string('brand_name', 255);

            $table->timestamps();

            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        // ---------------------------------------------------------------------
        // stock_items
        // ---------------------------------------------------------------------

        Schema::create('stock_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->string('item_code', 50)->unique();
            $table->string('item_name', 255);
            $table->text('item_name_ar')->nullable();
            $table->text('description')->nullable();
            $table->string('image', 100)->default('');
            $table->unsignedBigInteger('item_group_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();

            $table->unsignedInteger('current_stock')->default(0);
            $table->unsignedBigInteger('stock_uom_id');

            $table->decimal('tax_percent', 5, 2)->unsigned()->nullable();

            $table->boolean('is_active')->unsigned()->default(1);
            $table->boolean('is_sales_item')->unsigned()->default(0);

            $table->timestamps();

            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->foreign('item_group_id')->references('item_group_id')->on('stock_item_groups');
            $table->foreign('brand_id')->references('brand_id')->on('stock_brands');
            $table->foreign('stock_uom_id')->references('uom_id')->on('unit_of_measurements');
        });

        // ---------------------------------------------------------------------
        // stock_item_prices
        // ---------------------------------------------------------------------

        Schema::create('stock_item_prices', function (Blueprint $table) {
            $table->id('price_id');
            $table->unsignedBigInteger('item_id');

            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10, 2);

            $table->timestamps();

            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        // ---------------------------------------------------------------------
        // stock_movements
        // ---------------------------------------------------------------------

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id('stock_movement_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedInteger('stock_in')->default(0);
            $table->unsignedInteger('stock_out')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
