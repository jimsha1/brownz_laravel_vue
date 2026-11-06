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
        Schema::create('makeup_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beautician_id');
            $table->unsignedBigInteger('service_id');
            $table->string('makeup_service_name');
            $table->string('makeup_client_name');
            $table->string('booking_type');
            $table->string('makeup_contact_number');
            $table->string('makeup_client_email')->unique();
            $table->string('makeup_date_booked');
            $table->string('makeup_time_slot_id');
            $table->string('makeup_time_slot');
            $table->string('makeup_clent_address');
            $table->string('beautician_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makeup_bookings');
    }
};
