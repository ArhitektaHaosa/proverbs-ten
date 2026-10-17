<?php

namespace App\Console\Commands;

use App\Services\Import\ProverbImporter;
use Illuminate\Console\Command;

class ProverbsImport extends Command
{
    protected $signature = 'proverbs:import {lang} {--limit=} {--resume}';

    protected $description = 'Import proverbs for a language as drafts. Idempotent via text_hash.';

    public function handle(ProverbImporter $importer): int
    {
        $lang = $this->argument('lang');
        $limit = $this->option('limit');
        $resume = (bool) $this->option('resume');

        $this->info("Importing {$lang} (drafts only)...");

        $result = $importer->import($lang, $limit, $resume);

        $this->info("Imported: {$result['imported']}, skipped: {$result['skipped']}, updated: {$result['updated']}");

        return self::SUCCESS;
    }
}
