<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_years', function (Blueprint $table) {
            $table->foreign('closing_transaction_group_id')->references('id')->on('transaction_groups')->nullOnDelete();
            $table->foreign('opening_transaction_group_id')->references('id')->on('transaction_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('financial_years', function (Blueprint $table) {
            $table->dropForeign(['closing_transaction_group_id']);
            $table->dropForeign(['opening_transaction_group_id']);
        });
    }
};
