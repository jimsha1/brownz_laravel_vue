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
        Schema::create('employee_attendences', function (Blueprint $table) {
            $table->id('attendence_id');
            $table->string('employee_id')->nullable();
            $table->string('employee_code')->nullable();
            $table->string('user_id')->nullable();
            $table->dateTime('punch_date_time')->nullable();
            $table->string('punch_type')->nullable();
            $table->string('punch_date')->nullable();
            $table->string('punch_time')->nullable();
            $table->string('punch_status')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendences');
    }
};
