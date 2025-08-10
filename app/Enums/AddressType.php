<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum AddressType: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case IDENTITY = 1;
    case DOMICILE = 2;
    case OFFICE = 3;
    case OTHER = 4;

    public function label(): string
    {
        return match ($this) {
            self::IDENTITY => 'Identitas',
            self::DOMICILE => 'Domisili',
            self::OFFICE => 'Kantor',
            self::OTHER => 'Lainnya',
        };
    }
}
