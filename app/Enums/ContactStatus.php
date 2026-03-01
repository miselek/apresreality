<?php

namespace App\Enums;

enum ContactStatus: string
{
    case Aktivni = 'aktivni';
    case Ceka = 'ceka';
    case Uzavreno = 'uzavreno';
    case Archiv = 'archiv';

    public function label(): string
    {
        return match ($this) {
            self::Aktivni => 'Aktivní',
            self::Ceka => 'Čeká',
            self::Uzavreno => 'Uzavřeno',
            self::Archiv => 'Archiv',
        };
    }
}
