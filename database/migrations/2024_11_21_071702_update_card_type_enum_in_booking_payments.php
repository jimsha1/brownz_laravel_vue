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
        Schema::table('booking_payments', function (Blueprint $table) {
            DB::statement("ALTER TABLE `booking_payments` MODIFY `card_type` ENUM('master', 'visa', 'mada', 'credit') DEFAULT NULL");
            DB::statement("ALTER TABLE `booking_payments` MODIFY `payment_mode` ENUM('cash', 'card', 'online') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_payments', function (Blueprint $table) {
            DB::statement("ALTER TABLE `booking_payments` MODIFY `card_type` ENUM('master', 'visa') DEFAULT NULL");
            DB::statement("ALTER TABLE `booking_payments` MODIFY `payment_mode` ENUM('cash', 'card') NOT NULL");
        });
    }
};
