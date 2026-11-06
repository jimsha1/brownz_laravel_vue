<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('financial_year_id')->nullable()->after('date');
            $table->foreign('financial_year_id')->references('id')->on('financial_years')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transaction_groups', function (Blueprint $table) {
            $table->dropForeign(['financial_year_id']);
            $table->dropColumn('financial_year_id');
        });
    }
};
