<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add Edit Closed Count and grant it to roles that can already force-close or view Z-Out.
     */
    public function up(): void
    {
        $now = now();
        $permissionId = 95;

        DB::table('permissions')->updateOrInsert(
            ['id' => $permissionId],
            [
                'name' => 'edit_closed_count',
                'description' => 'Edit Closed Count',
                'guard_name' => 'web',
                'group' => 'x/z out',
                'display_order' => 95,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $sourcePermissionIds = DB::table('permissions')
            ->whereIn('name', ['force_close', 'former_zout'])
            ->pluck('id');

        if ($sourcePermissionIds->isEmpty()) {
            $this->forgetPermissionCache();
            return;
        }

        $roleIds = DB::table('role_has_permissions')
            ->whereIn('permission_id', $sourcePermissionIds)
            ->pluck('role_id')
            ->unique();

        foreach ($roleIds as $roleId) {
            $exists = DB::table('role_has_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->exists();

            if (! $exists) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }

        $directGrants = DB::table('model_has_permissions')
            ->whereIn('permission_id', $sourcePermissionIds)
            ->get(['model_type', 'model_id']);

        foreach ($directGrants as $grant) {
            $exists = DB::table('model_has_permissions')
                ->where('permission_id', $permissionId)
                ->where('model_type', $grant->model_type)
                ->where('model_id', $grant->model_id)
                ->exists();

            if (! $exists) {
                DB::table('model_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'model_type' => $grant->model_type,
                    'model_id' => $grant->model_id,
                ]);
            }
        }

        $this->forgetPermissionCache();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('name', 'edit_closed_count')->value('id');

        if (! $permissionId) {
            return;
        }

        DB::table('role_has_permissions')->where('permission_id', $permissionId)->delete();
        DB::table('model_has_permissions')->where('permission_id', $permissionId)->delete();
        DB::table('permissions')->where('id', $permissionId)->delete();

        $this->forgetPermissionCache();
    }

    private function forgetPermissionCache(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
