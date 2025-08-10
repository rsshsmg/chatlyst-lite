<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum ReligionType: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case Islam = 1;
    case Christianity = 2;
    case Protestan = 3;
    case Hinduism = 4;
    case Buddhism = 5;
    case Konghucu = 6;
    case Other = 7;

    public function label(): string
    {
        return match ($this) {
            self::Islam => 'Islam',
            self::Christianity => 'Christianity',
            self::Protestan => 'Protestan',
            self::Hinduism => 'Hinduism',
            self::Buddhism => 'Buddhism',
            self::Konghucu => 'Konghucu',
            self::Other => 'Other',
        };
    }
}
