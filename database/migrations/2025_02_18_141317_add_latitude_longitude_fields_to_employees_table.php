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
            $table->double('latitude')->nullable()->after('punch_status');
            $table->double('longitude')->nullable()->after('latitude');
            $table->string('check_in_image')->nullable()->after('longitude');
            $table->string('check_out_image')->nullable()->after('check_in_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('check_in_image');
            $table->dropColumn('check_out_image');
        });
    }
};
