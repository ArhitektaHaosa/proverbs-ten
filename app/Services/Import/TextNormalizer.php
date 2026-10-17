<?php

namespace App\Services\Import;

class TextNormalizer
{
    public function normalize(string $text): string
    {
        $text = \Normalizer::normalize($text, \Normalizer::FORM_C);
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);
        $text = preg_replace('/\s+/u', ' ', trim($text));
        return mb_strtolower($text);
    }

    public function hash(string $normalized): string
    {
        return hash('sha256', $normalized);
    }
}
