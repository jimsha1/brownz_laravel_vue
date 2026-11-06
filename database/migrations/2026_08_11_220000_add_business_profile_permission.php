<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure bussines_profile permission exists and grant it to admin roles.
     */
    public function up(): void
    {
        $now = now();

        DB::table('permissions')->updateOrInsert(
            ['id' => 30],
            [
                'name' => 'bussines_profile',
                'description' => 'View Business Profile',
                'guard_name' => 'web',
                'group' => 'settings',
                'display_order' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $permissionId = DB::table('permissions')->where('name', 'bussines_profile')->value('id');

        if (! $permissionId) {
            return;
        }

        $superAdminRoleId = DB::table('roles')->where('name', 'super_admin')->value('id');

        if ($superAdminRoleId) {
            $this->grantPermissionToRole($permissionId, $superAdminRoleId);
        }

        $settingsPermId = DB::table('permissions')->where('name', 'denomination')->value('id');

        if ($settingsPermId) {
            $roleIds = DB::table('role_has_permissions')
                ->where('permission_id', $settingsPermId)
                ->pluck('role_id');

            foreach ($roleIds as $roleId) {
                $this->grantPermissionToRole($permissionId, $roleId);
            }
        }
    }

    private function grantPermissionToRole(int $permissionId, int $roleId): void
    {
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('name', 'bussines_profile')->value('id');

        if (! $permissionId) {
            return;
        }

        DB::table('role_has_permissions')->where('permission_id', $permissionId)->delete();
        DB::table('permissions')->where('id', $permissionId)->delete();
    }
};
