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
            if (! Schema::hasColumn('float_amount', 'closed_by')) {
                $table->unsignedBigInteger('closed_by')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('float_amount', function (Blueprint $table) {
            if (Schema::hasColumn('float_amount', 'closed_by')) {
                $table->dropColumn('closed_by');
            }
        });
    }
};
