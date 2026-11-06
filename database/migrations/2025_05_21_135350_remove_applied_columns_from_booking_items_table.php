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
        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn(['applied_discount_price', 'applied_roundoff_price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->decimal('applied_discount_price', 10, 2)->unsigned()->default(0.00);
            $table->decimal('applied_roundoff_price', 10, 2)->unsigned()->default(0.00);
        });
    }
};
