<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store why a closed register count was corrected.
     */
    public function up(): void
    {
        Schema::table('float_amount', function (Blueprint $table) {
            if (! Schema::hasColumn('float_amount', 'edit_reason')) {
                $table->text('edit_reason')->nullable()->after('edited_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('float_amount', function (Blueprint $table) {
            if (Schema::hasColumn('float_amount', 'edit_reason')) {
                $table->dropColumn('edit_reason');
            }
        });
    }
};
