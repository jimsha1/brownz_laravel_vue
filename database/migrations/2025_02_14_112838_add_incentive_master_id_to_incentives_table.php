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
        Schema::table('incentives', function (Blueprint $table) {
            $table->unsignedBigInteger('incentive_master_id')->nullable()->after('incentive_id');
            $table->string('emp_name')->nullable()->after('emp_id');
            $table->decimal('salary', 10, 2)->default(0.00)->after('emp_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incentives', function (Blueprint $table) {
            $table->dropColumn(['incentive_master_id', 'emp_name', 'salary']);
        });
    }
};
