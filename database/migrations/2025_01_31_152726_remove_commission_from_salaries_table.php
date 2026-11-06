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
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn('commission');
            $table->dropColumn('incentive_amount');
            $table->dropColumn('net_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->decimal('commission', 10, 2)->default(0.00);
            $table->decimal('incentive_amount', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
        });
    }
};
