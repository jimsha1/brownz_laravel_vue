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
        Schema::table('consultations', function (Blueprint $table) {
            $table->enum('approve_status', ['pending', 'approved'])
                ->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
            $table->dropColumn('approve_status');
        });
    }
};
