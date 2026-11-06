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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('transaction_group_id')->nullable();
            $table->foreign('transaction_group_id')->references('id')->on('transaction_groups')->onDelete('cascade');

            $table->unsignedBigInteger('journal_id')->nullable();
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');

            $table->decimal('debit', 15, 2)->default(0.00);
            $table->decimal('credit', 15, 2)->default(0.00);
            $table->date('date');

            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('set null');

            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('vendor_id')->on('vendors')->onDelete('set null');

            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');       
                   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
