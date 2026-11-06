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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->string('booking_code')->nullable();
            $table->string('type')->nullable();
            $table->string('customer_name')->nullable();
            $table->integer('customer_age')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_mobile')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('beautician_id')->nullable();
            $table->string('beautician_name')->nullable();
            $table->string('beautician_designation')->nullable();
            $table->string('manager_name')->nullable();
            $table->integer('use_photo')->nullable();
            $table->string('customer_sign')->nullable();
            $table->string('beautician_sign')->nullable();
            $table->string('manager_sign')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
