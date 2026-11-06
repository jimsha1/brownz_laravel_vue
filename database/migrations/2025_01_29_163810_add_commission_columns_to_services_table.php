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
        Schema::table('split_service_amounts', function (Blueprint $table) {
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('split_amount');
            $table->decimal('commission_amount', 5, 2)->nullable()->after('commission_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('split_service_amounts', function (Blueprint $table) {
            $table->dropColumn(['commission_percentage', 'commission_amount']);
        });
    }
};
