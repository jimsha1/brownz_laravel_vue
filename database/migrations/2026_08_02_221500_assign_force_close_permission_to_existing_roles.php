<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure force_close exists and grant it to roles that already have Former Z-Out access.
     */
    public function up(): void
    {
        $now = now();

        $zoutPermId = DB::table('permissions')->where('name', 'former_zout')->value('id');

        $forceClosePermId = DB::table('permissions')->where('name', 'force_close')->value('id');
        if (! $forceClosePermId) {
            $forceClosePermId = 31;
            DB::table('permissions')->updateOrInsert(
                ['id' => $forceClosePermId],
                [
                    'name' => 'force_close',
                    'description' => 'Force Close',
                    'guard_name' => 'web',
                    'group' => 'x/z out',
                    'display_order' => 31,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        if (! $zoutPermId || ! $forceClosePermId) {
            return;
        }

        $roleIds = DB::table('role_has_permissions')
            ->where('permission_id', $zoutPermId)
            ->pluck('role_id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table('role_has_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $forceClosePermId)
                ->exists();

            if (! $exists) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $forceClosePermId,
                    'role_id' => $roleId,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $forceClosePermId = DB::table('permissions')->where('name', 'force_close')->value('id');

        if (! $forceClosePermId) {
            return;
        }

        DB::table('role_has_permissions')->where('permission_id', $forceClosePermId)->delete();
        DB::table('permissions')->where('id', $forceClosePermId)->delete();
    }
};
