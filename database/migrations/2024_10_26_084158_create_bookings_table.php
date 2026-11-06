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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('booking_code', 100)->unique()->nullable();
            $table->string('booking_barcode', 100)->unique()->nullable();

            $table->unsignedBigInteger('outlet_id');

            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client_name')->default('');
            $table->string('client_mobile', 50)->default('');
            $table->string('client_telephone', 50)->default('');
            $table->string('client_email')->default('');

            $table->enum('booking_source', [
                'Walk-in',
                'Website',
                'Android',
                'iOS',
            ]);

            $table->enum('booking_type', [
                'Sale',
                'Refund',
                'Cancel',
            ])->default('Sale');

            /* Payments */
            $table->decimal('booking_total', 10, 2)->default(0);
            $table->decimal('payment_total', 10, 2)->default(0);
            $table->enum('payment_status', [
                'pending',
                'initiated',
                'failed',
                'completed',
            ])->default('pending');

            $table->decimal('applied_discount_percent', 5, 2)->unsigned()->nullable();

            // Activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('canceled_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('canceled_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');

            $table->foreign('client_id')->references('client_id')->on('clients');
            $table->foreign('outlet_id')->references('outlet_id')->on('outlets');

            $table->timestamps();
            $table->timestamp('canceled_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
