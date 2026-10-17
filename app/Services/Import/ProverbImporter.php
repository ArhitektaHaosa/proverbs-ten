<?php

namespace App\Services\Import;

use App\Models\Language;
use App\Models\Proverb;
use Illuminate\Support\Facades\DB;

class ProverbImporter
{
    public function __construct(
        private WiktionaryClient $client,
        private TextNormalizer $normalizer,
    ) {}

    public function import(string $langCode, ?int $limit, bool $resume): array
    {
        $language = Language::where('code', $langCode)->firstOrFail();

        $imported = $skipped = $updated = 0;
        // Chunked, resumable cursor stored elsewhere.
        // Each entry: draft, source_type=wiktionary, attribution filled.
        // Dedup on (language_id, text_hash).

        return compact('imported', 'skipped', 'updated');
    }
}
