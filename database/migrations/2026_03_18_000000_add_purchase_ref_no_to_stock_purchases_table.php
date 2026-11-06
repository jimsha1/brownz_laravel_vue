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
        Schema::table('stock_purchases', function (Blueprint $table) {
            $table->string('purchase_ref_no', 50)->nullable()->after('purchase_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_purchases', function (Blueprint $table) {
            $table->dropColumn('purchase_ref_no');
        });
    }
};
