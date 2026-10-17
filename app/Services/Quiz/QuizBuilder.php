<?php

namespace App\Services\Quiz;

use App\Models\Proverb;
use App\Models\QuizRun;

class QuizBuilder
{
    public function start(string $type, int $playerId, ?string $date = null): QuizRun
    {
        $date = $date ?? now()->toDateString();
        $seed = hash('sha256', $type . $date . $playerId);

        // Build questions from approved data only. Store key server-side.
        $questions = $this->buildQuestions($type, $seed);

        return QuizRun::create([
            'player_id' => $playerId,
            'quiz_type' => $type,
            'run_date' => $date,
            'seed' => $seed,
            'questions' => $questions['public'],
            // answers key stays in the JSON under a server-only key or separate column in real impl
        ]);
    }

    public function score(QuizRun $run, array $answers): int
    {
        // Server-side only. Client never sees the key.
        return 0;
    }

    private function buildQuestions(string $type, string $seed): array
    {
        // Stub: real impl pulls approved proverbs, builds distractors.
        return ['public' => [], 'key' => []];
    }
}
