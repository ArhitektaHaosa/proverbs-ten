<?php

namespace App\Services\Import;

use App\Models\Language;
use App\Models\Proverb;
use Illuminate\Support\Str;

class ProverbImporter
{
    public function __construct(
        private WiktionaryClient $client,
        private TextNormalizer $normalizer,
    ) {}

    public function import(string $langCode, ?int $limit, bool $resume): array
    {
        $language = Language::where('code', $langCode)->firstOrFail();
        $category = $language->wiktionary_category ?? "{$language->name} proverbs";

        $imported = $skipped = $updated = 0;
        $continue = $resume ? $this->loadCursor($langCode) : null;
        $processed = 0;

        do {
            $batch = $this->client->fetchCategoryMembers($category, $continue);
            foreach ($batch['pages'] ?? [] as $page) {
                if ($limit && $processed >= $limit) {
                    break 2;
                }
                $processed++;

                $text = $page['title'] ?? '';
                if (strlen($text) < 10 || str_word_count($text) < 3) {
                    $skipped++;
                    continue;
                }

                $normalized = $this->normalizer->normalize($text);
                $hash = $this->normalizer->hash($normalized);

                $existing = Proverb::where('language_id', $language->id)
                    ->where('text_hash', $hash)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'source_revision' => $page['revision'] ?? $existing->source_revision,
                    ]);
                    $updated++;
                    continue;
                }

                Proverb::create([
                    'language_id' => $language->id,
                    'slug' => Str::slug($text) . '-' . Str::random(6),
                    'text' => $text,
                    'text_normalized' => $normalized,
                    'text_hash' => $hash,
                    'status' => 'draft',
                    'source_type' => 'wiktionary',
                    'source_url' => $page['url'] ?? null,
                    'source_title' => $page['title'] ?? null,
                    'source_revision' => $page['revision'] ?? null,
                    'license' => 'CC BY-SA',
                    'attribution' => 'Wiktionary',
                    'enrichment_score' => 0,
                ]);
                $imported++;
            }
            $continue = $batch['continue'] ?? null;
            $this->saveCursor($langCode, $continue);
        } while ($continue);

        return compact('imported', 'skipped', 'updated');
    }

    private function loadCursor(string $lang): ?string
    {
        return cache()->get("import.cursor.{$lang}");
    }

    private function saveCursor(string $lang, ?string $continue): void
    {
        cache()->put("import.cursor.{$lang}", $continue, now()->addDays(7));
    }
}
