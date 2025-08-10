<?php

namespace App\Traits;

trait EnumArrayableTrait
{
    public static function array(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function keys(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return collect(self::cases())->map(fn($case) => $case->label())->toArray();
    }
}
