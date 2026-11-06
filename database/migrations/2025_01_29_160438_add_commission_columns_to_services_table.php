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
            $table->date('start_date')->nullable()->after('line_total');
            $table->date('end_date')->nullable()->after('start_date');
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('end_date');
            $table->boolean('commission_status')->unsigned()->default(1)->after('commission_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_services', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'commission_percentage', 'commission_status']);
        });
    }
};
