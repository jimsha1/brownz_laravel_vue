<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_payments', 'prepaid_applied')) {
                $table->boolean('prepaid_applied')->default(false)->after('change_as_credit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_payments', function (Blueprint $table) {
            if (Schema::hasColumn('booking_payments', 'prepaid_applied')) {
                $table->dropColumn('prepaid_applied');
            }
        });
    }
};
