<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_purchase_edit_logs')) {
            return;
        }

        Schema::create('stock_purchase_edit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->text('edit_reason');
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->timestamps();

            $table->foreign('purchase_id')->references('purchase_id')->on('stock_purchases')->onDelete('cascade');
            $table->foreign('edited_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['purchase_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_purchase_edit_logs');
    }
};
