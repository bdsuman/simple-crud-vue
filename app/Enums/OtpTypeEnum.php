<?php

namespace App\Enums;

enum OtpTypeEnum: string
{
    case REGISTRATION = 'registration';
    case FORGOT_PASSWORD = 'forgot_password';
    case LOGIN = 'login';
    case CHANGE_EMAIL = 'change_email';

    public function label(): string {
        return match($this) {
            self::REGISTRATION => 'Registration',
            self::FORGOT_PASSWORD => 'Forgot Password',
            self::LOGIN => 'Login',
            self::CHANGE_EMAIL => 'Change Email',
        };
    }
}
