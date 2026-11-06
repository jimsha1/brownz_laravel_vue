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
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id('booking_service_id');

            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('shift_id')->default(0);

            /* Duplicating service details */
            $table->string('category_name', 255);
            $table->string('service_name', 255);
            $table->unsignedInteger('duration_minutes');
            $table->decimal('service_booking_price', 10, 2)->unsigned();

            $table->decimal('discount_percent', 5, 2)->unsigned()->nullable();
            $table->boolean('show_discount_persent')->default(0);
            $table->decimal('discount_amount', 10, 2)->unsigned()->default(0.00);

            $table->decimal('individual_discount_amt', 10, 2)->unsigned()->default(0);
            $table->decimal('individual_discount_percent', 5, 2)->unsigned()->default(0);

            $table->decimal('tax_percent', 5, 2)->unsigned()->nullable();
            $table->decimal('tax_amount', 10, 2)->unsigned()->default(0.00);

            $table->decimal('line_total', 10, 2)->unsigned();

            /* Appointment details */
            $table->date('appointment_date');
            $table->time('slot_start')->nullable();
            $table->time('slot_end')->nullable();
            $table->unsignedBigInteger('beautician_id')->nullable();

            $table->timestamp('client_checkin_at')->nullable();
            $table->unsignedBigInteger('client_checkin_by')->nullable();

            $table->timestamp('service_started_at')->nullable();
            $table->unsignedBigInteger('service_started_by')->nullable();

            $table->timestamp('service_finished_at')->nullable();
            $table->unsignedBigInteger('service_finished_by')->nullable();
            $table->text('service_finished_remarks')->nullable();

            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->text('rejection_remarks')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('approval_remarks')->nullable();

            $table->text('address')->nullable();
            $table->enum('service_type', [
                'Home',
                'Hotel',
                'Salon',
            ])->default('Salon');
            $table->string('latitude',100)->nullable();
            $table->string('longitude',100)->nullable();

            $table->enum('status', [
                'open',
                'completed',
                'rejected',
                'reopened',
                'refunded',
                'draft',
                'start'
            ])->default('open');

            // Activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->foreign('booking_id')->references('booking_id')->on('bookings');
            $table->foreign('service_id')->references('service_id')->on('services');
            $table->foreign('beautician_id')->references('beautician_id')->on('beauticians');
            $table->foreign('client_checkin_by')->references('id')->on('users');
            $table->foreign('service_started_by')->references('id')->on('users');
            $table->foreign('service_finished_by')->references('id')->on('users');
            $table->foreign('rejected_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_services');
    }
};
