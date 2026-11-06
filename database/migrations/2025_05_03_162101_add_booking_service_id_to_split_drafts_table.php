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
        Schema::table('split_drafts', function (Blueprint $table) {
            $table->integer('booking_service_id')->nullable()->after('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('split_drafts', function (Blueprint $table) {
            $table->dropColumn('booking_service_id');
        });
    }
};
