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
        Schema::create('auto_increment_masters', function (Blueprint $table) {
            $table->increments('auto_increment_id');
            $table->string('auto_code', 50)->unique();
            $table->string('auto_name', 50);
            $table->string('auto_prefix', 10);
            $table->unsignedTinyInteger('use_master_prefix')->default(1);
            $table->unsignedBigInteger('auto_increment_general_value')->default(1);
            $table->unsignedBigInteger('max_increment_value')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_increment_masters');
    }
};
