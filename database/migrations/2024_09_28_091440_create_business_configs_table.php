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
        Schema::create('business_configs', function (Blueprint $table) {
            $table->string('config_key', 30)->primary();
            $table->string('config_value');
            $table->string('validation')->default('');
            $table->string('input_type')->default('text');
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();

            // activity users
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_configs');
    }
};
