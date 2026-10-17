<?php

namespace App\Services\Players;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlayerService
{
    public function current(Request $request): ?Player
    {
        $token = $request->cookie('player_token');
        if (! $token) {
            return null;
        }

        return Player::where('token_hash', hash('sha256', $token))->first();
    }

    public function issue(): string
    {
        $token = Str::random(64);
        Player::create([
            'token_hash' => hash('sha256', $token),
            'nickname' => app(NicknameGenerator::class)->generate(),
        ]);

        return $token;
    }

    public function recoveryCode(Player $player): string
    {
        $code = Str::upper(Str::random(8));
        $player->update(['recovery_code_hash' => hash('sha256', $code)]);
        return $code; // show once
    }
}
