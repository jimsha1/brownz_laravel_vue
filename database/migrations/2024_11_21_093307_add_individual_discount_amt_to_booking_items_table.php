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
            $table->string('individual_discount_amt')->after('discount_amount')->nullable();
            $table->string('individual_discount_percent')->after('individual_discount_amt')->nullable();
            $table->string('show_discount_persent')->after('individual_discount_percent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn('individual_discount_amt');
            $table->dropColumn('individual_discount_percent');
            $table->dropColumn('show_discount_persent');
        });
    }
};
