<?php

namespace App\Enums;

enum ContactType: string
{
    case Kupec = 'kupec';
    case Prodavajici = 'prodavajici';
    case Najemnik = 'najemnik';
    case Investor = 'investor';

    public function label(): string
    {
        return match ($this) {
            self::Kupec => 'Kupec',
            self::Prodavajici => 'Prodávající',
            self::Najemnik => 'Nájemník',
            self::Investor => 'Investor',
        };
    }
}
