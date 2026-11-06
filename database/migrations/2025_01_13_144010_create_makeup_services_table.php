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
        Schema::create('makeup_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name')->nullable();
            $table->decimal('service_base_price', 10, 2)->default(0.00);
            $table->decimal('tax', 5, 2)->nullable()->default(0.00);
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makeup_services');
    }
};
