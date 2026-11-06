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
        Schema::table('booking_items', function (Blueprint $table) {
            $table->decimal('individual_discount_amt', 10, 2)->default(0)->change();
            $table->decimal('individual_discount_percent', 10, 2)->default(0)->change();
            $table->decimal('show_discount_persent', 10, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_items', function (Blueprint $table) {
            $table->string('individual_discount_amt')->nullable()->change();
            $table->string('individual_discount_percent')->nullable()->change();
            $table->string('show_discount_persent')->nullable()->change();
        });
    }
};
