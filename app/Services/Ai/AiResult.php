<?php

namespace App\Services\Ai;

class AiResult
{
    public function __construct(
        public readonly string $content,
        public readonly ?int $tokens = null,
        public readonly ?string $error = null,
    ) {}

    public function ok(): bool
    {
        return $this->error === null;
    }
}
