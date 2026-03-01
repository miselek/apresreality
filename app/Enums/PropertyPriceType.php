<?php

namespace App\Enums;

enum PropertyPriceType: string
{
    case Prodej = 'prodej';
    case Pronajem = 'pronajem';

    public function label(): string
    {
        return match ($this) {
            self::Prodej => 'Prodej',
            self::Pronajem => 'Pronájem',
        };
    }
}
