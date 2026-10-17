<?php

namespace App\Services\Import;

class WiktionaryClient
{
    public function rankCategories(int $top): array
    {
        // Stub: real implementation calls MediaWiki API categorymember counts.
        // Descriptive User-Agent includes config contact.
        // Never called from tests; use fixtures.
        return [];
    }

    public function fetchCategoryMembers(string $category, ?string $continue = null): array
    {
        // Stub: returns pages with continue token.
        return ['pages' => [], 'continue' => null];
    }
}
