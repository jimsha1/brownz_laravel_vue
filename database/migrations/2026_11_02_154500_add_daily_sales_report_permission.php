<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add the Daily Sales report permission and grant it to roles that already have Sales Report.
     */
    public function up(): void
    {
        $now = now();
        $permissionId = 99;

        DB::table('permissions')->updateOrInsert(
            ['id' => $permissionId],
            [
                'name' => 'daily_sales_report',
                'description' => 'Daily Sales Report',
                'guard_name' => 'web',
                'group' => 'report',
                'display_order' => 99,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $sourcePermissionIds = DB::table('permissions')
            ->where('name', 'sales_report')
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

    public function down(): void
    {
        $permissionId = DB::table('permissions')->where('name', 'daily_sales_report')->value('id');

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
