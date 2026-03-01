<?php

namespace App\Enums;

enum ContractStatus: string
{
    case Koncept = 'koncept';
    case Validace = 'validace';
    case Zvalidovano = 'zvalidovano';
    case Odeslano = 'odeslano';
    case Podepsano = 'podepsano';
    case Zamitnuto = 'zamitnuto';

    public function label(): string
    {
        return match ($this) {
            self::Koncept => 'Koncept',
            self::Validace => 'Čeká na validaci',
            self::Zvalidovano => 'Zvalidováno',
            self::Odeslano => 'Odesláno',
            self::Podepsano => 'Podepsáno',
            self::Zamitnuto => 'Zamítnuto',
        };
    }
}
