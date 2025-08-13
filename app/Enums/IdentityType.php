<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum IdentityType: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case KTP = 1;
    case SIM = 2;
    case STUDENTCARD = 3;
    case PASSPORT = 4;
    case OTHER = 99;

    public function label(): string
    {
        return match ($this) {
            self::KTP => 'KTP',
            self::SIM => 'SIM',
            self::PASSPORT => 'Passport',
            self::STUDENTCARD => 'Student Card',
            self::OTHER => 'Other',
        };
    }
}
