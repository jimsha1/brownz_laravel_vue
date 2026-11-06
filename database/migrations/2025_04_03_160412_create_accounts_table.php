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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id('account_id');
            $table->string('code')->unique();
            $table->string('name'); // e.g., "Cash", "Sales"
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->string('category')->nullable();
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->boolean('is_active')->unsigned()->default(1);
            $table->boolean('is_system_defined')->unsigned()->default(0);

            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');              

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
