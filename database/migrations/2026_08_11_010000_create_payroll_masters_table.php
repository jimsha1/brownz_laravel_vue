<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_masters', function (Blueprint $table) {
            $table->id('payroll_master_id');
            $table->string('month');
            $table->unsignedSmallInteger('year');
            $table->date('voucher_date')->nullable();
            $table->date('schedule_date')->nullable();
            $table->timestamps();

            $table->unique(['month', 'year']);
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->unsignedBigInteger('payroll_master_id')->nullable()->after('payroll_id');
            $table->foreign('payroll_master_id')
                ->references('payroll_master_id')
                ->on('payroll_masters')
                ->nullOnDelete();
        });

        $groups = DB::table('payrolls')
            ->select('month', 'year')
            ->groupBy('month', 'year')
            ->get();

        foreach ($groups as $group) {
            $sample = DB::table('payrolls')
                ->where('month', $group->month)
                ->where('year', $group->year)
                ->orderBy('payroll_id')
                ->first();

            if (! $sample) {
                continue;
            }

            $masterId = DB::table('payroll_masters')->insertGetId([
                'month' => $group->month,
                'year' => $group->year,
                'voucher_date' => $sample->voucher_date,
                'schedule_date' => $sample->schedule_date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('payrolls')
                ->where('month', $group->month)
                ->where('year', $group->year)
                ->update(['payroll_master_id' => $masterId]);
        }
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['payroll_master_id']);
            $table->dropColumn('payroll_master_id');
        });

        Schema::dropIfExists('payroll_masters');
    }
};
