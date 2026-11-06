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
        Schema::create('price_services', function (Blueprint $table) {
            $table->id('price_service_id');
            $table->integer('service_id');
            $table->integer('shift_id');
            $table->integer('service_type_id');
            $table->string('duration');
            $table->string('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_services');
    }
};
