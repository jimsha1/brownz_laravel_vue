<?php

use App\Models\Beautician;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Align beautician status with employee status for existing records.
     */
    public function up(): void
    {
        // Inactive employees → inactive beauticians
        DB::table('beauticians')
            ->join('employees', 'employees.user_id', '=', 'beauticians.beautician_id')
            ->where('employees.status', 'inactive')
            ->update(['beauticians.status' => Beautician::STATUS_INACTIVE]);

        // Active employees → active beauticians
        DB::table('beauticians')
            ->join('employees', 'employees.user_id', '=', 'beauticians.beautician_id')
            ->where('employees.status', 'active')
            ->update(['beauticians.status' => Beautician::STATUS_ACTIVE]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: cannot safely restore previous mismatched statuses
    }
};
