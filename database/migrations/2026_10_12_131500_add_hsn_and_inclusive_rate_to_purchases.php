<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->string('hsn_code', 20)->nullable()->after('item_name');
        });

        Schema::table('stock_purchases', function (Blueprint $table) {
            $table->decimal('round_off', 10, 2)->default(0)->after('total_purchase_amount');
        });

        Schema::table('stock_purchase_items', function (Blueprint $table) {
            $table->string('hsn_code', 20)->nullable()->after('item_id');
            $table->decimal('rate_incl_tax', 15, 2)->nullable()->after('cost_price');
            $table->decimal('discount_percent', 8, 4)->nullable()->after('discount');
        });

        Schema::table('stock_purchase_item_tracks', function (Blueprint $table) {
            $table->string('hsn_code', 20)->nullable()->after('item_id');
            $table->decimal('rate_incl_tax', 15, 2)->nullable()->after('cost_price');
            $table->decimal('discount_percent', 8, 4)->nullable()->after('discount');
        });
    }

    public function down(): void
    {
        Schema::table('stock_purchase_item_tracks', function (Blueprint $table) {
            $table->dropColumn(['hsn_code', 'rate_incl_tax', 'discount_percent']);
        });

        Schema::table('stock_purchase_items', function (Blueprint $table) {
            $table->dropColumn(['hsn_code', 'rate_incl_tax', 'discount_percent']);
        });

        Schema::table('stock_purchases', function (Blueprint $table) {
            $table->dropColumn('round_off');
        });

        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropColumn('hsn_code');
        });
    }
};
