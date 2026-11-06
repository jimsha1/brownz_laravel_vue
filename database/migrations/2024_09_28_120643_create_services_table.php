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
        Schema::create('services', function (Blueprint $table) {
            $table->id('service_id');
            $table->unsignedBigInteger('outlet_id');
            $table->unsignedInteger('category_id');

            $table->string('name');

            $table->text('short_description_en')->nullable();

            $table->unsignedInteger('duration_minutes');

            $table->decimal('service_base_price', 10, 2)->unsigned();
            $table->decimal('service_home_price', 10, 2)->unsigned()->nullable();

            $table->decimal('tax_percent', 5, 2)->unsigned()->nullable();

            $table->decimal('service_base_total', 10, 2)->unsigned();
            $table->decimal('service_home_total', 10, 2)->unsigned()->default(0);

            $table->boolean('allow_online_booking')->unsigned()->default(1);
            $table->boolean('show_on_website')->unsigned()->default(0);
            $table->boolean('is_active')->unsigned()->default(1);
            $table->unsignedInteger('display_order')->default(0);

            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->foreign('outlet_id')->references('outlet_id')->on('outlets');
            $table->foreign('category_id')->references('category_id')->on('service_categories');            

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
