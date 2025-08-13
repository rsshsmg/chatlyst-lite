<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum ReligionType: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case Islam = 1;
    case Christian = 2;
    case Catholic = 3;
    case Hindu = 4;
    case Buddhist = 5;
    case Confucian = 6;
    case Other = 7;

    public function label(): string
    {
        return match ($this) {
            self::Islam => 'Islam',
            self::Christian => 'Christianity',
            self::Catholic => 'Chatolic',
            self::Hindu => 'Hinduism',
            self::Buddhist => 'Buddhism',
            self::Confucian => 'Confucian',
            self::Other => 'Other',
        };
    }
}
