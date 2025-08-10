<?php

namespace App\Enums;

use App\Contracts\EnumArrayable;
use App\Traits\EnumArrayableTrait;

enum RelationType: int implements EnumArrayable
{
    use EnumArrayableTrait;

    case SELF = 1;
    case FATHER = 2;
    case MOTHER = 3;
    case CHILD = 4;
    case SIBLING = 5;
    case GRANDFATHER = 6;
    case GRANDMOTHER = 7;
    case GRANDCHILD = 8;
    case OTHER = 99;

    public function label(): string
    {
        return match ($this) {
            self::SELF => 'Self',
            self::FATHER => 'Father',
            self::MOTHER => 'Mother',
            self::CHILD => 'Child',
            self::SIBLING => 'Sibling',
            self::GRANDFATHER => 'Grandfather',
            self::GRANDMOTHER => 'Grandmother',
            self::GRANDCHILD => 'Grandchild',
            self::OTHER => 'Other',
        };
    }
}
