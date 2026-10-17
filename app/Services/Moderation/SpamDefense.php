<?php

namespace App\Services\Moderation;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class SpamDefense
{
    public function passes(array $input, string $ipHash): bool
    {
        if (! empty($input['website'])) {
            return false; // honeypot
        }

        $key = 'spam:' . $ipHash;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return false;
        }
        RateLimiter::hit($key, 60);

        $text = json_encode($input['payload'] ?? []);
        if (preg_match('/https?:\/\//i', $text)) {
            return false;
        }

        if (strlen($text) > 5000) {
            return false;
        }

        return true;
    }
}
