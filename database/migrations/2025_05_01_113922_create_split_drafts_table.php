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
        Schema::create('split_drafts', function (Blueprint $table) {
            $table->id('split_draft_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('beautician_id')->nullable();
            $table->string('service_name', 255)->nullable();
            $table->decimal('price', 10, 2)->unsigned();
            $table->decimal('total_amount', 10, 2)->unsigned()->nullable();
            $table->decimal('commission_percentage', 5, 2)->nullable();
            $table->decimal('commission_amount', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('split_drafts');
    }
};
