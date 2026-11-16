<?php

namespace App\Message;

/**
 * Pages the engineering crew about a critical repair ticket.
 */
final readonly class SoundRedAlert
{
    public function __construct(
        public int $ticketId,
    ) {
    }
}
