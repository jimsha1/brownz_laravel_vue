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
        Schema::table('booking_services', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_services', 'refunded_qty')) {
                $table->integer('refunded_qty')->default(0)->after('quantity');
            }
            if (! Schema::hasColumn('booking_services', 'refunded_booking_id')) {
                $table->integer('refunded_booking_id')->default(0)->after('refunded_qty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('booking_services', 'refunded_qty')) {
                $cols[] = 'refunded_qty';
            }
            if (Schema::hasColumn('booking_services', 'refunded_booking_id')) {
                $cols[] = 'refunded_booking_id';
            }
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
};
