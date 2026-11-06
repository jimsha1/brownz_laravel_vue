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
        Schema::table('stock_items', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('is_sales_item');
            $table->date('end_date')->nullable()->after('start_date');
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('end_date');
            $table->boolean('commission_status')->unsigned()->default(1)->after('commission_percentage');
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('beautician_id');
            $table->date('end_date')->nullable()->after('start_date');
            $table->decimal('commission_percentage', 5, 2)->nullable()->after('end_date');
            $table->boolean('commission_status')->unsigned()->default(0)->after('commission_percentage');
            $table->string('commission_split_status', 20)->nullable()->after('commission_status');
        });

        Schema::table('split_service_amounts', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('booking_service_id');
            $table->unsignedBigInteger('booking_item_id')->nullable()->after('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'commission_percentage', 'commission_status']);
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn([
                'start_date',
                'end_date',
                'commission_percentage',
                'commission_status',
                'commission_split_status',
            ]);
        });

        Schema::table('split_service_amounts', function (Blueprint $table) {
            $table->dropColumn(['item_id', 'booking_item_id']);
        });
    }
};
