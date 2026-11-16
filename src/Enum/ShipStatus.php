<?php

namespace App\Enum;

enum ShipStatus: string
{
    case InDock = 'in_dock';
    case Departed = 'departed';

    public function label(): string
    {
        return match ($this) {
            self::InDock => 'In dock',
            self::Departed => 'Departed',
        };
    }
}
