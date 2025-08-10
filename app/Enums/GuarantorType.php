<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum GuarantorType: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case BPJS = 1;
    case INSURANCE = 2;
    case CORPORATE = 3;
    case OTHERS = 4;

    public function label(): string
    {
        return match ($this) {
            self::BPJS => 'BPJS',
            self::INSURANCE => 'Insurance',
            self::CORPORATE => 'Corporate',
            self::OTHERS => 'Others',
        };
    }
}
