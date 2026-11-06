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
        Schema::create('outlets', function (Blueprint $table) {
            $table->id('outlet_id');
            $table->string('outlet_code', 10);
            $table->string('auto_prefix', 5)->default('');
            $table->string('name', 150);

            $table->string('phone');
            $table->string('email')->default('');
            $table->string('location')->default('');
            $table->string('address')->default('');
            $table->string('city')->default('');
            
            $table->string('building_name', 100)->default('');
            $table->string('post_office', 50)->default('');
            $table->string('postal_code', 10)->default('');
            $table->string('district', 50)->default('');
            $table->string('state', 50)->default('');
            $table->string('country', 50)->default('');

            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->unsigned()->default(1);

            $table->timestamps();

            // activity users
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
