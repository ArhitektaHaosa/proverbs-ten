<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SitePurgeCache extends Command
{
    protected $signature = 'site:purge-cache {--url=}';

    protected $description = 'Purge web-server cache for public pages.';

    public function handle(): int
    {
        // Implementation depends on CloudPanel/Nginx cache mechanism.
        $this->info('Cache purge requested.');
        return self::SUCCESS;
    }
}
