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
        Schema::create('split_service_amounts', function (Blueprint $table) {
            $table->id('split_service_amount_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('booking_service_id')->nullable();
            $table->unsignedBigInteger('beautician_id')->nullable();
            $table->string('service_name', 255)->nullable();
            $table->decimal('price', 10, 2)->unsigned(); 
            $table->decimal('total_amount', 10, 2)->unsigned()->nullable();
            $table->decimal('split_amount', 10, 2)->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('split_service_amounts');
    }
};
