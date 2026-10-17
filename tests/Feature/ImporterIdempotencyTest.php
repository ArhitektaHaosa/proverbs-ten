<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Proverb;
use App\Services\Import\TextNormalizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImporterIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_text_hash_is_unique_per_language(): void
    {
        $lang = Language::factory()->create();
        $normalizer = new TextNormalizer();
        $hash = $normalizer->hash($normalizer->normalize('A stitch in time saves nine'));

        Proverb::factory()->create([
            'language_id' => $lang->id,
            'text_hash' => $hash,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Proverb::factory()->create([
            'language_id' => $lang->id,
            'text_hash' => $hash,
        ]);
    }
}
