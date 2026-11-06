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
        Schema::create('user_has_outlets', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('outlet_id');

            $table->timestamp('created_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->primary(['user_id', 'outlet_id']);

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('outlet_id')->references('outlet_id')->on('outlets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_has_outlets');
    }
};
