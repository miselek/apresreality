<?php

namespace App\Enums;

enum TaskPriority: string
{
    case Vysoka = 'vysoka';
    case Stredni = 'stredni';
    case Nizka = 'nizka';

    public function label(): string
    {
        return match ($this) {
            self::Vysoka => 'Vysoká',
            self::Stredni => 'Střední',
            self::Nizka => 'Nízká',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Vysoka => 'red',
            self::Stredni => 'yellow',
            self::Nizka => 'gray',
        };
    }
}
