<?php

namespace App\Services\Ai;

class OpenRouterProvider implements AiProvider
{
    public function complete(array $messages, array $options = []): AiResult
    {
        // Stub: fallback provider.
        return new AiResult('{}');
    }
}
