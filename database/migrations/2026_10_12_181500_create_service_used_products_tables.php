<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_used_products', function (Blueprint $table) {
            $table->id('service_used_product_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 15, 3);
            $table->timestamps();

            $table->unique(['service_id', 'item_id'], 'service_used_products_service_item_unique');
            $table->index('item_id');
        });

        Schema::create('booking_service_products', function (Blueprint $table) {
            $table->id('booking_service_product_id');
            $table->unsignedBigInteger('booking_service_id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->string('item_name')->nullable();
            $table->string('uom_name')->nullable();
            $table->decimal('recipe_quantity', 15, 3)->default(0);
            $table->decimal('quantity', 15, 3)->default(0);
            $table->decimal('cost_price', 15, 3)->default(0);
            $table->decimal('pack_size', 15, 3)->default(1);
            $table->boolean('quantity_overridden')->default(false);
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->unique(['booking_service_id', 'item_id'], 'booking_service_products_line_item_unique');
            $table->index('booking_id');
            $table->index('item_id');
        });

        Schema::table('booking_services', function (Blueprint $table) {
            $table->boolean('used_products_snapshotted')->default(false)->after('service_id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('booking_service_id')->nullable()->after('booking_item_id');
            $table->unsignedBigInteger('booking_service_product_id')->nullable()->after('booking_service_id');
            $table->index('booking_service_product_id', 'stock_movements_booking_service_product_idx');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex('stock_movements_booking_service_product_idx');
            $table->dropColumn(['booking_service_id', 'booking_service_product_id']);
        });

        Schema::dropIfExists('booking_service_products');
        Schema::dropIfExists('service_used_products');

        Schema::table('booking_services', function (Blueprint $table) {
            $table->dropColumn('used_products_snapshotted');
        });
    }
};
