<?php

namespace App\Services\Quiz;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Leaderboard
{
    public function daily(): array
    {
        return Cache::remember('leaderboard.daily', 300, function () {
            return DB::table('quiz_runs')
                ->whereDate('run_date', today())
                ->whereNotNull('score')
                ->orderByDesc('score')
                ->orderBy('duration_ms')
                ->limit(50)
                ->get()
                ->all();
        });
    }
}
