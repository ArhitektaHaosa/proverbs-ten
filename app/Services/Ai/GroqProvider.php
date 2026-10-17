<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;

class GroqProvider implements AiProvider
{
    public function complete(array $messages, array $options = []): AiResult
    {
        // Stub: real call uses config('ai.groq.key') and model.
        // Privacy: only proverb text + language are sent by callers.
        return new AiResult('{}');
    }
}
