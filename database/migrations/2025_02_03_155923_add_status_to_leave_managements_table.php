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
        Schema::table('leave_managements', function (Blueprint $table) {
            Schema::table('leave_managements', function (Blueprint $table) {
                $table->enum('leave_status', ['pending', 'approved', 'rejected'])
                    ->default('pending')
                    ->after('leave_type');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_managements', function (Blueprint $table) {
            Schema::table('leave_managements', function (Blueprint $table) {
                $table->dropColumn('leave_status');
            });
        });
    }
};
