<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->decimal('pack_size', 12, 3)->default(1)->after('stock_uom_id');
        });

        // Stock, movements, and price quantity are stored in the stock unit (GM, ML, PCS).
        DB::statement('ALTER TABLE stock_items MODIFY current_stock DECIMAL(15,3) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stock_movements MODIFY stock_in DECIMAL(15,3) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stock_movements MODIFY stock_out DECIMAL(15,3) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stock_item_prices MODIFY quantity DECIMAL(15,3) NOT NULL DEFAULT 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropColumn('pack_size');
        });

        DB::statement('ALTER TABLE stock_items MODIFY current_stock INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stock_movements MODIFY stock_in INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stock_movements MODIFY stock_out INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE stock_item_prices MODIFY quantity INT UNSIGNED NOT NULL DEFAULT 1');
    }
};
