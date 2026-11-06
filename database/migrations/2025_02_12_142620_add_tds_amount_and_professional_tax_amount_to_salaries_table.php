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
            $table->decimal('TDS_amount', 10, 2)->after('TDS')->default(0.00);
            $table->decimal('professional_tax_amount', 10, 2)->after('professional_tax')->default(0.00);
            $table->decimal('net_salary', 10, 2)->after('incentive_percentage')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn('TDS_amount');
            $table->dropColumn('professional_tax_amount');
            $table->dropColumn('net_salary');
        });
    }
};
