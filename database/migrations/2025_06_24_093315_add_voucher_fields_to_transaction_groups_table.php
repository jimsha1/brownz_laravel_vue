<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaction_groups', function (Blueprint $table) {
            $table->string('voucher_type')->nullable()->default('Journal')->after('reference_type');
            $table->string('voucher_no')->default('Draft')->after('voucher_type');
            $table->boolean('is_create_voucher')->default(0)->after('voucher_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_groups', function (Blueprint $table) {
            $table->dropColumn(['voucher_type', 'voucher_no', 'is_create_voucher']);
        });
    }
};
