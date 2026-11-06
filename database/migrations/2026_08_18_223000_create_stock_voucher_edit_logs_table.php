<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_voucher_edit_logs')) {
            return;
        }

        Schema::create('stock_voucher_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_group_id')->nullable();
            $table->string('voucher_no', 100)->nullable();
            $table->text('edit_reason');
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->timestamps();

            $table->foreign('transaction_group_id')->references('id')->on('transaction_groups')->nullOnDelete();
            $table->foreign('edited_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['transaction_group_id', 'created_at']);
            $table->index('voucher_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_voucher_edit_logs');
    }
};
