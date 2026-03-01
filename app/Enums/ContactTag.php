<?php

namespace App\Enums;

enum ContactTag: string
{
    case Horka = 'horka';
    case OK = 'ok';
    case Studena = 'studena';
    case Uzavreno = 'uzavreno';

    public function label(): string
    {
        return match ($this) {
            self::Horka => 'Horká',
            self::OK => 'OK',
            self::Studena => 'Studená',
            self::Uzavreno => 'Uzavřeno',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Horka => 'red',
            self::OK => 'blue',
            self::Studena => 'gray',
            self::Uzavreno => 'green',
        };
    }
}
