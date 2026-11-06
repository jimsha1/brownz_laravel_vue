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
        Schema::create('hair_declarations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('consultation_id');
            $table->integer('info_accuracy')->default(0);
            $table->integer('chemical_risk_acknowledged')->default(0);
            $table->integer('patch_test_agreed')->default(0);
            $table->integer('result_variation_acknowledged')->default(0);
            $table->integer('post_service_care_acknowledged')->default(0);
            $table->integer('liability_release')->default(0);
            $table->integer('service_cost_informed')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hair_declarations');
    }
};
