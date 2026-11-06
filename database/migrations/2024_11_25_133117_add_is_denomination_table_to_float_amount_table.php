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
        Schema::table('float_amount', function (Blueprint $table) {
            $table->integer('is_denomination_table')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('float_amount', function (Blueprint $table) {
            $table->dropColumn('is_denomination_table');
        });
    }
};
