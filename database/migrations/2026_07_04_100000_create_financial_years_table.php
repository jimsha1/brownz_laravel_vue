<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_years', function (Blueprint $table) {
            $table->id();
            $table->string('label', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->boolean('is_current')->default(false);
            $table->decimal('net_profit_loss', 15, 2)->nullable();
            $table->unsignedBigInteger('closing_transaction_group_id')->nullable();
            $table->unsignedBigInteger('opening_transaction_group_id')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->unsignedBigInteger('opened_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['start_date', 'end_date']);
            $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('opened_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_years');
    }
};
