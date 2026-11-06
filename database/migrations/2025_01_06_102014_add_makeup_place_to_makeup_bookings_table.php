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
        Schema::table('makeup_bookings', function (Blueprint $table) {
            $table->string('makeup_place')->after('makeup_clent_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('makeup_bookings', function (Blueprint $table) {
            $table->dropColumn('makeup_place');
        });
    }
};
