<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContributionRequest;
use App\Models\Contribution;
use App\Services\Moderation\SpamDefense;
use App\Services\Players\PlayerService;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    public function store(StoreContributionRequest $request, PlayerService $players, SpamDefense $spam)
    {
        $player = $players->current($request);
        $ipHash = hash_hmac('sha256', $request->ip(), config('app.key'));

        if (! $spam->passes($request->validated(), $ipHash)) {
            return back()->withErrors(['spam' => 'Rejected']);
        }

        Contribution::create([
            'proverb_id' => $request->input('proverb_id'),
            'type' => $request->input('type'),
            'payload' => $request->input('payload'),
            'player_id' => $player?->id,
            'status' => 'pending',
            'ip_hash' => $ipHash,
            'license_ack' => true,
        ]);

        return back()->with('status', 'Submitted for review');
    }
}
