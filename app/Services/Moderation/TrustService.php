<?php

namespace App\Services\Moderation;

use App\Models\Player;

class TrustService
{
    public function maybePromote(Player $player): void
    {
        $threshold = config('site.trust_after_approved');
        if ($player->approved_count >= $threshold && ! $player->trusted) {
            $player->update(['trusted' => true]);
        }
    }
}
