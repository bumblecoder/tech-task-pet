<?php

namespace App\Enum;

enum Sex: string
{
    case Male = 'Male';
    case Female = 'Female';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
        };
    }

    public static function choices(): array
    {
        return [
            self::Male->label() => self::Male->value,
            self::Female->label() => self::Female->value,
        ];
    }

    public static function values(): array
    {
        return array_map(fn(self $sex) => $sex->value, self::cases());
    }
}
