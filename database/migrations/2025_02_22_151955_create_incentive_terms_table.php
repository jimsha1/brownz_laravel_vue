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
        Schema::create('incentive_terms', function (Blueprint $table) {
            $table->id('incentive_terms_id');
            $table->string('incentive_title')->nullable();
            $table->integer('incentive_range_from')->nullable();
            $table->integer('incentive_range_to')->nullable();
            $table->decimal('incentive_percentage', 10, 2)->default(0);
            $table->string('status')->default('active');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentive_terms');
    }
};
