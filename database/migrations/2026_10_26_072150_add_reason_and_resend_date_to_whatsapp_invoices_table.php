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
        Schema::table('whatsapp_invoices', function (Blueprint $table) {
            $table->text('message_status')->nullable()->after('status');
            $table->text('reason')->nullable()->after('message_status');
            $table->timestamp('resend_date')->nullable()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_invoices', function (Blueprint $table) {
            $table->dropColumn(['message_status','reason', 'resend_date']);
        });
    }
};
