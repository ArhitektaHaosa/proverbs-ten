<?php

namespace App\Console\Commands;

use App\Models\Proverb;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FillProverbOfDay extends Command
{
    protected $signature = 'proverbs:fill-day';

    protected $description = 'Fill next 365 days of Proverb of the Day from approved indexable proverbs.';

    public function handle(): int
    {
        $existing = DB::table('proverb_of_day')->pluck('date')->all();
        $candidates = Proverb::public()
            ->where('enrichment_score', '>=', config('site.seo_min_enrichment_score'))
            ->inRandomOrder()
            ->limit(400)
            ->get();

        // Real impl: fill missing dates, no repeats in cycle, language variety.
        $this->info('PoTD filler stub. Candidates: ' . $candidates->count());

        return self::SUCCESS;
    }
}
