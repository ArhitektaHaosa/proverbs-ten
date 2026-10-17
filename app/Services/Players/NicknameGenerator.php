<?php

namespace App\Services\Players;

class NicknameGenerator
{
    public function generate(): string
    {
        $adjectives = file(resource_path('wordlists/adjectives.txt'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: ['Calm'];
        $animals = file(resource_path('wordlists/animals.txt'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: ['Fox'];

        $adj = $adjectives[array_rand($adjectives)];
        $animal = $animals[array_rand($animals)];
        $num = random_int(10, 99);

        return "{$adj}{$animal}{$num}";
    }
}
