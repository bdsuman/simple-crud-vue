<?php

use App\Enums\UserRoleEnum;
use App\Enums\UserGenderEnum;
use App\Enums\AppLanguageEnum;
use App\Enums\LoginTypeEnum;
use App\Enums\UserAccountStatusEnum;
use App\Enums\UserVoiceStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role', array_column(UserRoleEnum::cases(), 'value'))->default(UserRoleEnum::USER);
            $table->string('full_name', 255);

            $table->enum('gender', array_column(UserGenderEnum::cases(), 'value'))->nullable();
            $table->date('date_of_birth')->nullable();

            $table->string('email', 255)->unique();
            $table->string('email_verify_token', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password', 255)->nullable();

            $table->enum('language', array_column(AppLanguageEnum::cases(), 'value'))->default(AppLanguageEnum::DE);

            $table->string('avatar', 255)->nullable();
            $table->string('voice', 255)->nullable();
            $table->string('voice_title', 255)->nullable();
            $table->string('eleven_lab_voice_id', 255)->nullable();
            $table->string('cloned_voice')->nullable();
            $table->text('voice_text')->nullable();
            $table->enum('voice_status', array_column(UserVoiceStatusEnum::cases(), 'value'))->default(UserVoiceStatusEnum::NOT_GIVEN);

            $table->enum('status', array_column(UserAccountStatusEnum::cases(), 'value'))->default(UserAccountStatusEnum::ACTIVE);

            $table->boolean('profile_setup_completed')->default(false);
            $table->boolean('personality_profiling_completed')->default(false);
            $table->boolean('agreed_to_tos')->default(true);

            $table->string('password_reset_token', 255)->nullable();

            $table->enum('login_type', array_column(LoginTypeEnum::cases(), 'value'))->default(LoginTypeEnum::REGULAR);

            $table->timestamp('last_login_at')->nullable();

            $table->string('facebook_email', 255)->nullable();
            $table->text('facebook_user_id')->nullable()->unique();
            $table->string('google_email', 255)->nullable();
            $table->text('google_user_id')->nullable()->unique();
            $table->string('apple_email', 255)->nullable();
            $table->text('apple_user_id')->nullable()->unique();

            // Self-referencing FKs
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Helpful indexes
            $table->index(['status', 'role']);
            $table->index('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
