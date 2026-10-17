<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Proverb;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProverbFactory extends Factory
{
    protected $model = Proverb::class;

    public function definition(): array
    {
        $text = $this->faker->sentence();
        $normalized = mb_strtolower(preg_replace('/\s+/', ' ', trim($text)));

        return [
            'language_id' => Language::factory(),
            'slug' => Str::slug($text) . '-' . Str::random(6),
            'text' => $text,
            'text_normalized' => $normalized,
            'text_hash' => hash('sha256', $normalized),
            'status' => 'draft',
            'source_type' => 'wiktionary',
            'enrichment_score' => 0,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'published_at' => now(),
            'enrichment_score' => 4,
        ]);
    }
}
