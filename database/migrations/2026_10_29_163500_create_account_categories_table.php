<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('name');
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->boolean('is_system')->default(0);
            $table->timestamps();

            $table->unique(['type', 'name']);
        });

        $now = now();
        $rows = DB::table('accounts')
            ->select('category', 'type', DB::raw('MAX(is_system_defined) as is_system'))
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->groupBy('category', 'type')
            ->get();

        foreach ($rows as $row) {
            DB::table('account_categories')->insert([
                'name' => $row->category,
                'type' => $row->type,
                'is_system' => (int) $row->is_system === 1 ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_categories');
    }
};
