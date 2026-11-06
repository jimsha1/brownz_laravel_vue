<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Speed up service report filters on status + appointment_date.
     */
    public function up(): void
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $table->index(['status', 'appointment_date'], 'booking_services_status_appointment_date_index');
            $table->index('service_name', 'booking_services_service_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $table->dropIndex('booking_services_status_appointment_date_index');
            $table->dropIndex('booking_services_service_name_index');
        });
    }
};
