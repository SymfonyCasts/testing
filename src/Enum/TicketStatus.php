<?php

namespace App\Enum;

enum TicketStatus: string
{
    case Open = 'open';
    case Resolved = 'resolved';
}
