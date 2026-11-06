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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['HRA_percentage', 'TA_percentage', 'DA_percentage','date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('HRA_percentage', 5, 2)->nullable();
            $table->decimal('TA_percentage', 5, 2)->nullable();
            $table->decimal('DA_percentage', 5, 2)->nullable();
            $table->date('date')->nullable();
        });
    }
};
