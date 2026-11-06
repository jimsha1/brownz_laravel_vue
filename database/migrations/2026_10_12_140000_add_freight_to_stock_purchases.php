<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_purchases', function (Blueprint $table) {
            $table->decimal('freight_amount', 15, 2)->default(0)->after('round_off');
            $table->decimal('freight_tax_percent', 5, 2)->default(0)->after('freight_amount');
        });

        Schema::table('stock_purchase_tracks', function (Blueprint $table) {
            $table->decimal('freight_amount', 15, 2)->default(0)->after('payment_method');
            $table->decimal('freight_tax_percent', 5, 2)->default(0)->after('freight_amount');
        });
    }

    public function down(): void
    {
        Schema::table('stock_purchase_tracks', function (Blueprint $table) {
            $table->dropColumn(['freight_amount', 'freight_tax_percent']);
        });

        Schema::table('stock_purchases', function (Blueprint $table) {
            $table->dropColumn(['freight_amount', 'freight_tax_percent']);
        });
    }
};
