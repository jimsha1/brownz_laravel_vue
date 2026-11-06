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
        Schema::table('stock_purchase_items', function (Blueprint $table) {
            $table->integer('refunded_qty')->default(0)->after('quantity');
            $table->unsignedBigInteger('refunded_purchase_id')->nullable()->after('purchase_id');
            $table->enum('status', ['delivered', 'refunded'])->default('delivered')->after('purchase_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_purchase_items', function (Blueprint $table) {
            $table->dropColumn(['refunded_qty','refunded_purchase_id','status']);
        });
    }
};
