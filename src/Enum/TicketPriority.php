<?php

namespace App\Enum;

enum TicketPriority: string
{
    case Low = 'low';
    case Normal = 'normal';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Normal => 'Normal',
            self::Critical => 'Critical',
        };
    }
}
