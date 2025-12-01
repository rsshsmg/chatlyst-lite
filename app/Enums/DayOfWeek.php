<?php

namespace App\Enums;

enum DayOfWeek: int
{
    // case SUNDAY = 0;
    case MONDAY = 1;
    case TUESDAY = 2;
    case WEDNESDAY = 3;
    case THURSDAY = 4;
    case FRIDAY = 5;
    case SATURDAY = 6;

    public function label(): string
    {
        return match ($this) {
            self::MONDAY => 'Senin',
            self::TUESDAY => 'Selasa',
            self::WEDNESDAY => 'Rabu',
            self::THURSDAY => 'Kamis',
            self::FRIDAY => 'Jumat',
            self::SATURDAY => 'Sabtu',
            // self::SUNDAY => 'Minggu',
        };
    }

    public function dayName(): string
    {
        return match ($this) {
            self::MONDAY => 'Monday',
            self::TUESDAY => 'Tuesday',
            self::WEDNESDAY => 'Wednesday',
            self::THURSDAY => 'Thursday',
            self::FRIDAY => 'Friday',
            self::SATURDAY => 'Saturday',
        };
    }

    public static function options(): array
    {
        $arr = [];
        foreach (self::cases() as $case) {
            $arr[$case->value] = $case->label();
        }

        return $arr;
    }
}
