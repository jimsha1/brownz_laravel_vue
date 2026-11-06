<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('booking_edit_logs')) {
            return;
        }

        Schema::create('booking_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->text('edit_reason');
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->timestamps();

            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            $table->foreign('edited_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['booking_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_edit_logs');
    }
};
