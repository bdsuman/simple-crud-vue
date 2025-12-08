<?php

namespace App\Enums;

enum OtpDestinationTypeEnum: string
{
    case EMAIL = 'email';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'Email'
        };
    }
}
