<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure purchase_edit exists and grant it to roles that already have purchase access.
     */
    public function up(): void
    {
        $now = now();

        $purchasePermId = DB::table('permissions')->where('name', 'purchase')->value('id');

        $editPermId = DB::table('permissions')->where('name', 'purchase_edit')->value('id');
        if (! $editPermId) {
            $editPermId = 92;
            DB::table('permissions')->updateOrInsert(
                ['id' => $editPermId],
                [
                    'name' => 'purchase_edit',
                    'description' => 'Edit Purchase',
                    'guard_name' => 'web',
                    'group' => 'stock',
                    'display_order' => 92,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        if (! $purchasePermId || ! $editPermId) {
            return;
        }

        $roleIds = DB::table('role_has_permissions')
            ->where('permission_id', $purchasePermId)
            ->pluck('role_id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table('role_has_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $editPermId)
                ->exists();

            if (! $exists) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $editPermId,
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
        $editPermId = DB::table('permissions')->where('name', 'purchase_edit')->value('id');

        if (! $editPermId) {
            return;
        }

        DB::table('role_has_permissions')->where('permission_id', $editPermId)->delete();
    }
};
