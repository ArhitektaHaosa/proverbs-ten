<?php

namespace App\Services\Ai;

class AiRouter
{
    public function __construct(
        private GroqProvider $groq,
        private OpenRouterProvider $openRouter,
    ) {}

    public function complete(array $messages, array $options = []): AiResult
    {
        $primary = config('ai.primary');
        $fallback = config('ai.fallback');

        try {
            $result = $this->driver($primary)->complete($messages, $options);
            if ($result->ok()) {
                return $result;
            }
        } catch (\Throwable $e) {
            // fall through
        }

        return $this->driver($fallback)->complete($messages, $options);
    }

    private function driver(string $name): AiProvider
    {
        return match ($name) {
            'groq' => $this->groq,
            'openrouter' => $this->openRouter,
            default => $this->groq,
        };
    }
}
