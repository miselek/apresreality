<?php

namespace App\Enums;

enum ContactSource: string
{
    case Sreality = 'sreality';
    case Doporuceni = 'doporuceni';
    case Fermakleri = 'fermakleri';
    case SocialniSite = 'socialni_site';
    case VlastniWeb = 'vlastni_web';
    case Jiny = 'jiny';

    public function label(): string
    {
        return match ($this) {
            self::Sreality => 'Sreality',
            self::Doporuceni => 'Doporučení',
            self::Fermakleri => 'Férmakléři',
            self::SocialniSite => 'Sociální sítě',
            self::VlastniWeb => 'Vlastní web',
            self::Jiny => 'Jiný',
        };
    }
}
