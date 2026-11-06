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
        // Denomination table
        Schema::create('denomination', function (Blueprint $table) {
            $table->id('denomination_id');
            $table->string('title');
            $table->string('denomination');
            $table->dateTime('added_date');
            $table->dateTime('updated_date');
            $table->boolean('status')->default(1);
        });

        // float amount
        Schema::create('float_amount', function (Blueprint $table) {
            $table->id('float_amt_id');
            $table->unsignedBigInteger('user_id');            
            $table->string('amonut');
            $table->boolean('is_closed')->default(0);
            $table->boolean('print_bill')->default(0);
            $table->dateTime('added_date');
            $table->dateTime('updated_date');
            $table->boolean('status')->default(1);
        });

        // is_denomination_table
        Schema::create('is_denomination_table', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_need')->default(1);
            $table->dateTime('added_date');
            $table->dateTime('updated_date');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denomination');
        Schema::dropIfExists('float_amount');
        Schema::dropIfExists('is_denomination_table');      
    }
};
