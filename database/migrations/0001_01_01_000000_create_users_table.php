<?php

use App\Models\User;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            /* login attributes */
            $table->string('username')->unique();
            $table->string('email');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            /* general info */
            $table->string('profile_photo_path')->nullable();
            $table->date('date_of_birth')->nullable();

            /* contact info */
            $table->string('mobile', 20)->default('');
            $table->string('telephone', 20)->default('');
            $table->text('address')->nullable();
            $table->string('city', 200)->default('');
            $table->string('state', 200)->default('');
            $table->string('zip', 10)->default('');

            // login track
            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_failed_login_attempt')->nullable();
            $table->timestamp('last_password_change')->nullable();


            $table->enum('gender', [
                User::GENDER_UNSPECIFIED,
                User::GENDER_MALE,
                User::GENDER_FEMALE,
            ])->default(User::GENDER_UNSPECIFIED);


            $table->integer('shift_id')->default(0);

            $table->enum('user_status', [
                User::USER_STATUS_PENDING,
                User::USER_STATUS_ACTIVE,
                User::USER_STATUS_INACTIVE,
                User::USER_STATUS_BLOCKED,
                User::USER_STATUS_DELETED,
            ])->default(User::USER_STATUS_ACTIVE);

            $table->boolean('is_logged')->default(0);
            $table->string('week_off', 100)->nullable();

            // Activity users
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');

            $table->rememberToken();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
