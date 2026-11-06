<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {

            $table->unsignedBigInteger('vendor_id')->nullable()->after('booking_item_id');
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->nullOnDelete();

            $table->enum('type', ['purchase', 'purchase_return'])->nullable()->after('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id', 'type']);
        });
    }
};