<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum MaritalStatus: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case Single = 1;
    case Married = 2;
    case Widow = 3;
    case Widower = 4;
    case Other = 5;

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Single',
            self::Married => 'Married',
            self::Widow => 'Widow',
            self::Widower => 'Widower',
            self::Other => 'Other',
        };
    }
}
