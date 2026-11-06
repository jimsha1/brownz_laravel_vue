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
        Schema::table('booking_services', function (Blueprint $table) {
            $table->text('closing_remarks')->nullable()->after('service_finished_remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $table->dropColumn('closing_remarks');
        });
    }
};
