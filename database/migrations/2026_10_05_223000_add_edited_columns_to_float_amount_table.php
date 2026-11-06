<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Record who corrected a closed register count, without changing the original close.
     */
    public function up(): void
    {
        Schema::table('float_amount', function (Blueprint $table) {
            if (! Schema::hasColumn('float_amount', 'edited_by')) {
                $table->unsignedBigInteger('edited_by')->nullable()->after('closed_by');
            }
            if (! Schema::hasColumn('float_amount', 'edited_at')) {
                $table->dateTime('edited_at')->nullable()->after('edited_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('float_amount', function (Blueprint $table) {
            if (Schema::hasColumn('float_amount', 'edited_at')) {
                $table->dropColumn('edited_at');
            }
            if (Schema::hasColumn('float_amount', 'edited_by')) {
                $table->dropColumn('edited_by');
            }
        });
    }
};
