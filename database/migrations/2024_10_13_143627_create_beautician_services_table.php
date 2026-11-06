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
        Schema::create('beautician_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beautician_id');
            $table->unsignedBigInteger('service_id');

            $table->timestamps();

  
            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->foreign('beautician_id')->references('beautician_id')->on('beauticians');
            $table->foreign('service_id')->references('service_id')->on('services');

            $table->boolean('status')->default(1)->comment('1: Active, 0: InActive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beautician_services');
    }
};
