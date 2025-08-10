<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum ContactType: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case Phone = 1;
    case Email = 2;
    case Whatsapp = 3;

    public function label(): string
    {
        return match ($this) {
            self::Phone => 'Phone',
            self::Email => 'Email',
            self::Whatsapp => 'WhatsApp',
        };
    }
}
