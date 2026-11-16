<?php

namespace App\Enum;

enum SystemStatus: string
{
    case Ok = 'ok';
    case Fault = 'fault';

    public function label(): string
    {
        return match ($this) {
            self::Ok => 'OK',
            self::Fault => 'FAULT',
        };
    }
}
