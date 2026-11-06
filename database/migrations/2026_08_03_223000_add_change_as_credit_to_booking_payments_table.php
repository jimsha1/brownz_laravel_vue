<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('booking_payments', 'change_as_credit')) {
                $table->boolean('change_as_credit')->default(false)->after('amount_given');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_payments', function (Blueprint $table) {
            if (Schema::hasColumn('booking_payments', 'change_as_credit')) {
                $table->dropColumn('change_as_credit');
            }
        });
    }
};
