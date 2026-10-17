<?php

namespace App\Services\Quiz;

class QuizBuilder
{
    public function build(string $type, int $playerId, string $seed): array
    {
        // Server-side questions + answer key stored in quiz_runs.
        // Client never receives the key.
        // Stub.
        return ['questions' => [], 'key' => []];
    }
}
