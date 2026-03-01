<?php

namespace App\Enums;

enum InterestType: string
{
    case Zajemce = 'zajemce';
    case Navsteva = 'navsteva';
    case Rezervace = 'rezervace';

    public function label(): string
    {
        return match ($this) {
            self::Zajemce => 'Zájemce',
            self::Navsteva => 'Návštěva',
            self::Rezervace => 'Rezervace',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Zajemce => 'blue',
            self::Navsteva => 'yellow',
            self::Rezervace => 'green',
        };
    }
}
