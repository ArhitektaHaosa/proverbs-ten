<?php

namespace App\Services\Moderation;

class SpamDefense
{
    public function passes(array $input, string $ipHash): bool
    {
        // Honeypot, min-time, rate limits, no URLs, length limits.
        // Stub.
        return true;
    }
}
