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
        Schema::table('employee_attendences', function (Blueprint $table) {
            $table->string('employee_name')->nullable()->after('employee_code');
            $table->date('date')->nullable()->after('employee_name');
            $table->time('check_in')->nullable()->after('date');
            $table->time('check_out')->nullable()->after('check_in');
            $table->string('attendance_status')->in('present','absent')->default('present')->after('check_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_attendences', function (Blueprint $table) {
            $table->dropColumn('employee_name');
            $table->dropColumn('date');
            $table->dropColumn('check_in');
            $table->dropColumn('check_out');
            $table->dropColumn('attendance_status');
        });
    }
};
