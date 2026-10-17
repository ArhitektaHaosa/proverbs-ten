<?php

return [
    'primary' => env('AI_PRIMARY', 'groq'),
    'fallback' => env('AI_FALLBACK', 'openrouter'),
    'throttle_seconds' => (int) env('AI_THROTTLE_SECONDS', 4),
    'daily_limit' => (int) env('AI_DAILY_LIMIT', 500),
    'prompt_version' => env('AI_PROMPT_VERSION', 'v1'),
    'groq' => [
        'key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL'),
    ],
    'openrouter' => [
        'key' => env('OPENROUTER_API_KEY'),
        'model' => env('OPENROUTER_MODEL'),
    ],
];
