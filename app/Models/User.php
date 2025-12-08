<?php

namespace App\Models;

use App\Enums\UserRoleEnum;
use App\Enums\LoginTypeEnum;
use App\Builders\UserBuilder;
use App\Enums\UserGenderEnum;
use App\Enums\AppLanguageEnum;
use App\Enums\UserAccountStatusEnum;
use App\Traits\FirebaseNotifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, FirebaseNotifiable;

    protected $fillable = [
        'role',
        'full_name',
        'gender',
        'date_of_birth',
        'email',
        'email_verify_token',
        'email_verified_at',
        'password',
        'language',
        'avatar',
        'voice',
        'eleven_lab_voice_id',
        'voice_title',
        'status',
        'profile_setup_completed',
        'personality_profiling_completed',
        'agreed_to_tos',
        'password_reset_token',
        'login_type',
        'last_login_at',
        'facebook_user_id',
        'facebook_email',
        'google_email',
        'google_user_id',
        'apple_email',
        'apple_user_id',
        'deleted_by',
        'created_by',
        'cloned_voice',
        'voice_text',
        'provider',
        'provider_id',
    ];

    protected $hidden = [
        'password',
        'password_reset_token',
        'email_verify_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'date_of_birth' => 'date',
        'profile_setup_completed' => 'boolean',
        'personality_profiling_completed' => 'boolean',
        'agreed_to_tos' => 'boolean',
        'deleted_at' => 'datetime',
        'role' => UserRoleEnum::class,
        'gender' => UserGenderEnum::class,
        'language' => AppLanguageEnum::class,
        'status' => UserAccountStatusEnum::class,
        'login_type' => LoginTypeEnum::class,
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            do {
                $uuid = 'UID' . random_int(10000, 99999);
            } while (self::where('uuid', $uuid)->exists());

            $user->uuid = $uuid;
            if ($user->isProfileSetupCompleted()) {
                $user->profile_setup_completed = true;
            }
        });

        static::updating(function ($user) {
            if ($user->isProfileSetupCompleted()) {
                $user->profile_setup_completed = true;
            }
        });
    }

    static function getUserByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    static function getActiveUserByEmail($email)
    {
        return self::where('email', $email)->where('status', UserAccountStatusEnum::ACTIVE)->first();
    }

    public function newEloquentBuilder($query)
    {
        return new UserBuilder($query);
    }

    protected $appends = ['avatar_url', 'voice_url', 'voice_cover_image_url', 'voice_description', 'cloned_voice_url'];

    public function getAvatarUrlAttribute()
    {
        if (is_null($this->avatar)) {
            return null;
        }

        if (preg_match('@http@', $this->avatar)) {
            return $this->avatar;
        }

        if (config('filesystems.default') == 'exoscale') {
            return Storage::disk('exoscale')->publicUrl($this->avatar);
        }

        return Storage::disk(config('filesystems.default'))->url($this->avatar);
    }

    public function getVoiceUrlAttribute()
    {
        if (is_null($this->voice)) {
            return null;
        }

        if (preg_match('@http@', $this->voice)) {
            return $this->voice;
        }

        if (config('filesystems.default') == 'exoscale') {
            return Storage::disk('exoscale')->publicUrl($this->voice);
        }

        return Storage::disk(config('filesystems.default'))->url($this->voice);
    }

    public function getClonedVoiceUrlAttribute()
    {
        if (is_null($this->cloned_voice)) {
            return null;
        }

        if (preg_match('@http@', $this->cloned_voice)) {
            return $this->cloned_voice;
        }

        if (config('filesystems.default') == 'exoscale') {
            return Storage::disk('exoscale')->publicUrl($this->cloned_voice);
        }

        return Storage::disk(config('filesystems.default'))->url($this->cloned_voice);
    }

    public function voiceDescription(): Attribute
    {
        return Attribute::get(fn() => 'Own Voice');
    }

    public function voiceCoverImageUrl(): Attribute
    {
        return Attribute::get(fn() => asset('images/background_cover_image.png'));
    }

    public function createdBy()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(self::class, 'deleted_by');
    }

    private function isProfileSetupCompleted()
    {
        return filled($this->gender)
            && filled($this->date_of_birth)
            && filled($this->full_name);
            // && UserProfileQuestionSubmission::where('user_id', $this->id)->exists();

        // && filled($this->voice)
        // && filled($this->voice_text);

        // && filled($this->cloned_voice) // Removed cloned voice as a requirement for profile setup completion
    }

}
