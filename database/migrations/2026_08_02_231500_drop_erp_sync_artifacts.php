<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop unused ERP sync tables/columns leftover from removed integration.
     */
    public function up(): void
    {
        Schema::dropIfExists('failed_erp');
        Schema::dropIfExists('erp_port');

        if (Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'is_erp_sync')) {
            Schema::table('bookings', function ($table) {
                $table->dropColumn('is_erp_sync');
            });
        }
    }

    public function down(): void
    {
        // Intentionally not recreating removed ERP integration schema.
    }
};
