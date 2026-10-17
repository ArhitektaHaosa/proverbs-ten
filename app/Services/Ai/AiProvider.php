<?php

namespace App\Services\Ai;

interface AiProvider
{
    public function complete(array $messages, array $options = []): AiResult;
}
