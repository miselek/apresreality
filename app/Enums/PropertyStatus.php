<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case Nabor = 'nabor';
    case Priprava = 'priprava';
    case Inzerce = 'inzerce';
    case Prohlidky = 'prohlidky';
    case Rezervace = 'rezervace';
    case Smlouva = 'smlouva';
    case Prodano = 'prodano';
    case Archiv = 'archiv';

    public function label(): string
    {
        return match ($this) {
            self::Nabor => 'Nábor',
            self::Priprava => 'Příprava',
            self::Inzerce => 'Inzerce',
            self::Prohlidky => 'Prohlídky',
            self::Rezervace => 'Rezervace',
            self::Smlouva => 'Smlouva',
            self::Prodano => 'Prodáno',
            self::Archiv => 'Archiv',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Nabor => 'gray',
            self::Priprava => 'blue',
            self::Inzerce => 'green',
            self::Prohlidky => 'yellow',
            self::Rezervace => 'purple',
            self::Smlouva => 'indigo',
            self::Prodano => 'gold',
            self::Archiv => 'gray',
        };
    }

    public function order(): int
    {
        return match ($this) {
            self::Nabor => 1,
            self::Priprava => 2,
            self::Inzerce => 3,
            self::Prohlidky => 4,
            self::Rezervace => 5,
            self::Smlouva => 6,
            self::Prodano => 7,
            self::Archiv => 8,
        };
    }

    public static function nextStatus(self $current): ?self
    {
        return match ($current) {
            self::Nabor => self::Priprava,
            self::Priprava => self::Inzerce,
            self::Inzerce => self::Prohlidky,
            self::Prohlidky => self::Rezervace,
            self::Rezervace => self::Smlouva,
            self::Smlouva => self::Prodano,
            self::Prodano => null,
            self::Archiv => null,
        };
    }
}
