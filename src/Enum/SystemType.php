<?php

namespace App\Enum;

enum SystemType: string
{
    case WarpCore = 'warp_core';
    case Shields = 'shields';
    case LifeSupport = 'life_support';
    case Navigation = 'navigation';
    case Comms = 'comms';

    public function label(): string
    {
        return match ($this) {
            self::WarpCore => 'Warp core',
            self::Shields => 'Shields',
            self::LifeSupport => 'Life support',
            self::Navigation => 'Navigation',
            self::Comms => 'Comms',
        };
    }
}
