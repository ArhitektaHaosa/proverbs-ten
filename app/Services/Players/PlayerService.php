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

        $hash = hash('sha256', $token);
        return Player::where('token_hash', $hash)->first();
    }

    public function issue(Request $request): string
    {
        $token = Str::random(64);
        Player::create([
            'token_hash' => hash('sha256', $token),
            'nickname' => app(NicknameGenerator::class)->generate(),
        ]);

        return $token;
    }
}
