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
        Schema::create('service_categories', function (Blueprint $table) {
            $table->increments('category_id');
            $table->unsignedInteger('parent_id')->nullable();

            $table->string('name');

            $table->text('tagline_en')->nullable();

            $table->text('description_en')->nullable();

            $table->text('long_description_en')->nullable();

            $table->text('banner_image')->nullable();
            $table->text('square_image')->nullable();

            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('show_on_website')->unsigned()->default(0);

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
        Schema::dropIfExists('service_categories');
    }
};
