<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Cache;

class AiRouter
{
    public function __construct(
        private GroqProvider $groq,
        private OpenRouterProvider $openRouter,
    ) {}

    public function complete(array $messages, array $options = []): AiResult
    {
        if ($this->dailyLimitReached()) {
            return new AiResult('', error: 'daily limit');
        }

        $primary = config('ai.primary');
        $fallback = config('ai.fallback');

        $result = $this->tryDriver($primary, $messages, $options);
        if ($result->ok()) {
            $this->recordCall();
            return $result;
        }

        $result = $this->tryDriver($fallback, $messages, $options);
        if ($result->ok()) {
            $this->recordCall();
        }

        return $result;
    }

    private function tryDriver(string $name, array $messages, array $options): AiResult
    {
        if ($this->circuitOpen($name)) {
            return new AiResult('', error: 'circuit open');
        }

        try {
            $result = $this->driver($name)->complete($messages, $options);
            if ($result->ok()) {
                $this->resetCircuit($name);
                return $result;
            }
            $this->recordFailure($name);
            return $result;
        } catch (\Throwable $e) {
            $this->recordFailure($name);
            return new AiResult('', error: $e->getMessage());
        }
    }

    private function driver(string $name): AiProvider
    {
        return match ($name) {
            'openrouter' => $this->openRouter,
            default => $this->groq,
        };
    }

    private function dailyLimitReached(): bool
    {
        $count = (int) Cache::get('ai.daily.count', 0);
        return $count >= config('ai.daily_limit');
    }

    private function recordCall(): void
    {
        Cache::increment('ai.daily.count');
        Cache::put('ai.daily.count', Cache::get('ai.daily.count'), now()->endOfDay());
    }

    private function circuitOpen(string $name): bool
    {
        return (int) Cache::get("ai.circuit.{$name}.failures", 0) >= 5;
    }

    private function recordFailure(string $name): void
    {
        Cache::increment("ai.circuit.{$name}.failures");
    }

    private function resetCircuit(string $name): void
    {
        Cache::forget("ai.circuit.{$name}.failures");
    }
}
