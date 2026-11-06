<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Beautician was granted Edit Closed Count because that role can view Z-Out.
     * A user who also has Super Admin kept the button after Super Admin was unchecked.
     */
    public function up(): void
    {
        $roleId = DB::table('roles')->where('name', 'beautition')->value('id');
        $permissionId = DB::table('permissions')->where('name', 'edit_closed_count')->value('id');

        if ($roleId && $permissionId) {
            DB::table('role_has_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $permissionId)
                ->delete();
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $roleId = DB::table('roles')->where('name', 'beautition')->value('id');
        $permissionId = DB::table('permissions')->where('name', 'edit_closed_count')->value('id');

        if (! $roleId || ! $permissionId) {
            return;
        }

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

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
