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
        Schema::create('beauticians', function (Blueprint $table) {
            $table->unsignedBigInteger('beautician_id')->primary();

            $table->string('beautician_code', 100);
            $table->unsignedBigInteger('created_outlet_id');

            $table->date('join_date');

            $table->unique(['beautician_code', 'created_outlet_id']);

            $table->text('short_bio_en')->nullable();

            $table->text('long_bio_en')->nullable();

            $table->foreign('beautician_id')->references('id')->on('users');
            $table->foreign('created_outlet_id')->references('outlet_id')->on('outlets');

            $table->enum('status', ['A','I'])->default('A')->comment('A = Active, I = Inactive');
        });

        // ---------------------------------------------------------------------
        // beautician_leave_days
        // ---------------------------------------------------------------------

        Schema::create('beautician_leave_days', function (Blueprint $table) {
            $table->id('leave_id');
            $table->unsignedBigInteger('beautician_id');
            $table->date('day');
            $table->enum('leave_mode', [
                'full_day',
                'first_half',
                'second_half',
            ]);

            $table->timestamps();

            // activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->foreign('beautician_id')->references('beautician_id')->on('beauticians');

            $table->unique(['beautician_id', 'day']);
        });        
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beautician_leave_days');
        Schema::dropIfExists('beauticians');        
    }
};
