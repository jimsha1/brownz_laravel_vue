<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add the Edit Log report permission and grant it to roles that already have a report.
     */
    public function up(): void
    {
        $now = now();
        $permissionId = 96;

        DB::table('permissions')->updateOrInsert(
            ['id' => $permissionId],
            [
                'name' => 'edit_log_report',
                'description' => 'Edit Log Report',
                'guard_name' => 'web',
                'group' => 'report',
                'display_order' => 96,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $sourcePermissionIds = DB::table('permissions')
            ->whereIn('name', [
                'sales_report',
                'client_report',
                'vendor_report',
                'tax_report',
                'service_report',
                'beautician_report',
                'consultation_report',
            ])
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
        $permissionId = DB::table('permissions')->where('name', 'edit_log_report')->value('id');

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
