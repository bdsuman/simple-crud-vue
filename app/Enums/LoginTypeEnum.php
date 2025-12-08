<?php

namespace App\Enums;

enum LoginTypeEnum: string
{
    case EMAIL = 'email';
    case GOOGLE = 'google';
    case APPLE = 'apple';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email',
            self::GOOGLE => 'Google',
            self::APPLE => 'Apple',
        };
    }
}
