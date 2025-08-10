<?php

namespace App\Contracts;

interface EnumArrayable
{
    public static function array(): array;

    public static function keys(): array;

    public static function labels(): array;
}
