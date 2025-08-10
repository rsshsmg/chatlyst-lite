<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum BloodType: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case APositive = 1;
    case ANegative = 2;
    case BPositive = 3;
    case BNegative = 4;
    case ABPositive = 5;
    case ABNegative = 6;
    case OPositive = 7;
    case ONegative = 8;

    public function label(): string
    {
        return match ($this) {
            self::APositive => 'A+',
            self::ANegative => 'A-',
            self::BPositive => 'B+',
            self::BNegative => 'B-',
            self::ABPositive => 'AB+',
            self::ABNegative => 'AB-',
            self::OPositive => 'O+',
            self::ONegative => 'O-',
        };
    }
}
