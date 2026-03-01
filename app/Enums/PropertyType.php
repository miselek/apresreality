<?php

namespace App\Enums;

enum PropertyType: string
{
    case Byt = 'byt';
    case Dum = 'dum';
    case Pozemek = 'pozemek';
    case Komercni = 'komercni';

    public function label(): string
    {
        return match ($this) {
            self::Byt => 'Byt',
            self::Dum => 'Dům',
            self::Pozemek => 'Pozemek',
            self::Komercni => 'Komerční',
        };
    }
}
