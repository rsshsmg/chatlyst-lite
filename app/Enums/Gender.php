<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum Gender: string implements EnumArrayable
{
    use EnumArrayableTrait;

    case Male = 'm';
    case Female = 'f';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Male => 'blue',
            self::Female => 'pink',
        };
    }


    public function icon(): string
    {
        return match ($this) {
            self::Male => 'icon-male',
            self::Female => 'icon-female',
        };
    }
}
