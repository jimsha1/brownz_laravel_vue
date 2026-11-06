<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_services', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_services', 'refunded_from_booking_service_id')) {
                $table->unsignedBigInteger('refunded_from_booking_service_id')->nullable()->default(null)
                    ->after('refunded_booking_id');
            }
        });

        Schema::table('booking_items', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_items', 'refunded_from_booking_item_id')) {
                $table->unsignedBigInteger('refunded_from_booking_item_id')->nullable()->default(null)
                    ->after('refunded_booking_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_services', function (Blueprint $table) {
            if (Schema::hasColumn('booking_services', 'refunded_from_booking_service_id')) {
                $table->dropColumn('refunded_from_booking_service_id');
            }
        });

        Schema::table('booking_items', function (Blueprint $table) {
            if (Schema::hasColumn('booking_items', 'refunded_from_booking_item_id')) {
                $table->dropColumn('refunded_from_booking_item_id');
            }
        });
    }
};
