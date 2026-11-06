<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_year_opening_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('financial_year_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('financial_year_id')->references('id')->on('financial_years')->onDelete('cascade');
            $table->foreign('account_id')->references('account_id')->on('accounts')->onDelete('cascade');
            $table->unique(['financial_year_id', 'account_id'], 'fy_opening_bal_year_acct_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_year_opening_balances');
    }
};
