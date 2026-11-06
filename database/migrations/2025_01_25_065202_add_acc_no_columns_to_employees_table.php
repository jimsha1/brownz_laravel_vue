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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('acc_no')->after('emergency_contact')->nullable();
            $table->string('ifsc_code')->after('acc_no')->nullable();
            $table->string('branch_name')->after('ifsc_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('acc_no');
            $table->dropColumn('ifsc_code');
            $table->dropColumn('branch_name');
        });
    }
};
