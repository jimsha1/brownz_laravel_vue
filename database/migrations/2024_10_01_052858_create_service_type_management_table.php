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
        Schema::create('service_type_managements', function (Blueprint $table) {
            $table->id('service_type_management_id');
            $table->string('service_type_name');
            $table->boolean('status')->default(1);
            $table->boolean('collect_location')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_type_management');
    }
};
