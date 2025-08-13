<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum BloodType: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case A = 1;
    case APositive = 11;
    case ANegative = 12;
    case B = 2;
    case BPositive = 21;
    case BNegative = 22;
    case AB = 3;
    case ABPositive = 31;
    case ABNegative = 32;
    case O = 4;
    case OPositive = 41;
    case ONegative = 42;
    case UNKNOWN = 5;

    public function label(): string
    {
        return match ($this) {
            self::A => 'A',
            self::APositive => 'A+',
            self::ANegative => 'A-',
            self::B => 'B',
            self::BPositive => 'B+',
            self::BNegative => 'B-',
            self::AB => 'AB',
            self::ABPositive => 'AB+',
            self::ABNegative => 'AB-',
            self::O => 'O',
            self::OPositive => 'O+',
            self::ONegative => 'O-',
            self::UNKNOWN => 'Unknown',
        };
    }
}
