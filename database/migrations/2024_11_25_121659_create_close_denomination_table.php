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
        Schema::create('close_denomination', function (Blueprint $table) {
            $table->id('close_denomination_id');
            $table->unsignedBigInteger('float_amt_id');
            $table->string('title', 200)->nullable();
            $table->string('amount', 200)->nullable();
            $table->string('qty', 200)->nullable();
            $table->string('total', 200);
            $table->timestamp('added_date')->nullable();
            $table->timestamp('updated_date')->nullable();
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('close_denomination');
    }
};
