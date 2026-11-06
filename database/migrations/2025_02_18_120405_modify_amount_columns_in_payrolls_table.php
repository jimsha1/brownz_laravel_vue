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
            $table->renameColumn('HRA_amount', 'HRA');
            $table->renameColumn('TA_amount', 'TA');
            $table->renameColumn('DA_amount', 'DA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->renameColumn('HRA', 'HRA_amount');
            $table->renameColumn('TA', 'TA_amount');
            $table->renameColumn('DA', 'DA_amount');
        });
    }
};
