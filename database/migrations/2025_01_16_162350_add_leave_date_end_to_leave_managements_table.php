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
            $table->date('leave_date_end')->nullable()->after('leave_date');
            $table->enum('leave_mode', ['paid', 'unpaid', 'week_off'])->nullable()->after('leave_date_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_managements', function (Blueprint $table) {
            $table->dropColumn('leave_date_end');
            $table->dropColumn('leave_mode');
        });
    }
};
