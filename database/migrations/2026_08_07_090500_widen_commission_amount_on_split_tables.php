<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * commission_amount was decimal(5,2) (max 999.99). Large qty / line totals
     * (e.g. 31500 @ 5% = 1575) overflow and abort finalize before accounting posts.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE split_service_amounts MODIFY commission_amount DECIMAL(12,2) NULL');

        if (Schema::hasTable('split_drafts') && Schema::hasColumn('split_drafts', 'commission_amount')) {
            DB::statement('ALTER TABLE split_drafts MODIFY commission_amount DECIMAL(12,2) NULL');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE split_service_amounts MODIFY commission_amount DECIMAL(5,2) NULL');

        if (Schema::hasTable('split_drafts') && Schema::hasColumn('split_drafts', 'commission_amount')) {
            DB::statement('ALTER TABLE split_drafts MODIFY commission_amount DECIMAL(5,2) NULL');
        }
    }
};
