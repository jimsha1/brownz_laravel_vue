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
        Schema::table('stock_purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('vendor_id');
            $table->enum('purchase_type', ['purchase', 'refund'])
                ->default('purchase')
                ->after('parent_id');
            $table->string('refund_invoice_no')->nullable()->after('purchase_invoice_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_purchases', function (Blueprint $table) {
            $table->dropColumn(['parent_id','purchase_type','refund_invoice_no']);
        });
    }
};
