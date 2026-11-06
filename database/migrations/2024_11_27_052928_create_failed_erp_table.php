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
        Schema::create('failed_erp', function (Blueprint $table) {
            $table->id('failed_erp_id');
            $table->text('erp_url')->nullable();
            $table->text('bug_list')->nullable();
            $table->string('booking_code', 200)->nullable();
            $table->string('erp_name', 200)->nullable();
            $table->text('json_data')->nullable();
            $table->string('row_count')->nullable();
            $table->integer('records_inserted')->default(0);
            $table->integer('records_updated')->default(0);
            $table->integer('failed')->default(0);
            $table->dateTime('added_date')->nullable();
            $table->integer('status')->default(1);
            $table->string('type',100)->nullable();
            
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_erp');
    }
};
