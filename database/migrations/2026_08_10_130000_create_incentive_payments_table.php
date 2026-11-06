<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incentive_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incentive_id');
            $table->unsignedBigInteger('transaction_group_id');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date')->nullable();
            $table->string('payment_mode', 30)->nullable();
            $table->timestamps();

            $table->foreign('incentive_id')->references('incentive_id')->on('incentives')->onDelete('cascade');
            $table->foreign('transaction_group_id')->references('id')->on('transaction_groups')->onDelete('cascade');
            $table->index(['transaction_group_id']);
            $table->index(['incentive_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive_payments');
    }
};
