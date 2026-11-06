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
        Schema::create('employee_punches', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->nullable();
            $table->string('employee_code')->nullable();
            $table->string('punch_type')->nullable();
            $table->dateTime('punch_date_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_punches');
    }
};
