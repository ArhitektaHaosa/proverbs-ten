<?php

namespace App\Http\Controllers;

use App\Services\Players\PlayerService;
use App\Services\Quiz\QuizBuilder;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function start(Request $request, PlayerService $players, QuizBuilder $builder)
    {
        $player = $players->current($request);
        if (! $player) {
            return response()->json(['error' => 'token required'], 403);
        }

        $run = $builder->start($request->input('type', 'daily'), $player->id);

        // Return only public questions. Never the key.
        return response()->json(['questions' => $run->questions]);
    }

    public function submit(Request $request, QuizBuilder $builder)
    {
        // Load run, score server-side, store score + duration.
        return response()->json(['score' => 0]);
    }
}
