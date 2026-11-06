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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id('salary_id');
            $table->unsignedBigInteger('emp_id');
            $table->string('basic_salary')->nullable();
            $table->decimal('TA', 10, 2)->default(0);
            $table->decimal('DA', 10, 2)->default(0);
            $table->decimal('HRA', 10, 2)->default(0);
            $table->decimal('special_allowance', 10, 2)->default(0);
            $table->decimal('TDS', 10, 2)->default(0);
            $table->decimal('professional_tax', 10, 2)->default(0);
            $table->decimal('target_value', 10, 2)->default(0);
            $table->decimal('incentive_percentage', 5, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->decimal('incentive_amount', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
