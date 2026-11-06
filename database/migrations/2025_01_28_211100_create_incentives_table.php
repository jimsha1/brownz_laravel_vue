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
        Schema::create('incentives', function (Blueprint $table) {
            $table->id('incentive_id');
            $table->integer('emp_id')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('target_amount', 10, 2)->default(0);
            $table->decimal('incentive_percentage', 10, 2)->default(0);
            $table->decimal('incentive_amount', 10, 2)->default(0);
            $table->string('incentive_status')->default('active');
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
        Schema::dropIfExists('incentives');
    }
};
