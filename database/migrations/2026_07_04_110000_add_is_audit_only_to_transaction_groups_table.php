<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_groups', function (Blueprint $table) {
            $table->boolean('is_audit_only')->default(false)->after('is_create_voucher');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_groups', function (Blueprint $table) {
            $table->dropColumn('is_audit_only');
        });
    }
};
