<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure purchase_return exists and grant it to roles that already have purchase access.
     */
    public function up(): void
    {
        $now = now();

        $purchasePermId = DB::table('permissions')->where('name', 'purchase')->value('id');

        $returnPermId = DB::table('permissions')->where('name', 'purchase_return')->value('id');
        if (! $returnPermId) {
            $returnPermId = 93;
            DB::table('permissions')->updateOrInsert(
                ['id' => $returnPermId],
                [
                    'name' => 'purchase_return',
                    'description' => 'Purchase Return',
                    'guard_name' => 'web',
                    'group' => 'stock',
                    'display_order' => 93,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        if (! $purchasePermId || ! $returnPermId) {
            return;
        }

        $roleIds = DB::table('role_has_permissions')
            ->where('permission_id', $purchasePermId)
            ->pluck('role_id');

        foreach ($roleIds as $roleId) {
            $exists = DB::table('role_has_permissions')
                ->where('role_id', $roleId)
                ->where('permission_id', $returnPermId)
                ->exists();

            if (! $exists) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $returnPermId,
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
        $returnPermId = DB::table('permissions')->where('name', 'purchase_return')->value('id');

        if (! $returnPermId) {
            return;
        }

        DB::table('role_has_permissions')->where('permission_id', $returnPermId)->delete();
        DB::table('permissions')->where('id', $returnPermId)->delete();
    }
};
