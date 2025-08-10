<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum MaritalStatus: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case Single = 1;
    case Married = 2;
    case Divorced = 3;
    case Widowed = 4;

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Single',
            self::Married => 'Married',
            self::Divorced => 'Divorced',
            self::Widowed => 'Widowed',
        };
    }
}
