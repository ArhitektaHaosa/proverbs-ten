<?php

namespace App\Console\Commands;

use App\Services\Import\WiktionaryClient;
use Illuminate\Console\Command;

class ProverbsRankLanguages extends Command
{
    protected $signature = 'proverbs:rank-languages {--top=40}';

    protected $description = 'Count Wiktionary proverb category members and print a ranked table. Writes nothing.';

    public function handle(WiktionaryClient $client): int
    {
        $this->info('Ranking Wiktionary proverb categories (no writes)...');

        // Implementation uses MediaWiki API category info.
        // Owner confirms top N; those go into config/languages.php.
        $rows = $client->rankCategories((int) $this->option('top'));

        $this->table(['Language', 'Code', 'Category', 'Count'], $rows);

        $this->comment('Owner must confirm the top 10 and update config/languages.php.');

        return self::SUCCESS;
    }
}
