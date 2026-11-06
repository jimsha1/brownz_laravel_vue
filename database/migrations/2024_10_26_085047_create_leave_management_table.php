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
        Schema::create('leave_managements', function (Blueprint $table) {
            $table->id('leave_id');
            $table->unsignedBigInteger('beautician_id');
            $table->date('leave_date');
            $table->text('reason')->nullable();
            $table->enum('leave_type', [
                'full',
                'first_half',
                'second_half',
            ]);
            

            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->foreign('beautician_id')->references('beautician_id')->on('beauticians');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_management');
    }
};
