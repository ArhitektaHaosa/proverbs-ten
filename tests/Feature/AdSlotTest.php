<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdSlotTest extends TestCase
{
    public function test_ad_slot_empty_when_disabled(): void
    {
        config(['site.adsense_enabled' => false]);
        $html = view('components.ad-slot', ['name' => 'test'])->render();
        $this->assertStringNotContainsString('ad-slot', $html);
    }

    public function test_ads_txt_404_when_disabled(): void
    {
        config(['site.adsense_enabled' => false]);
        $this->get('/ads.txt')->assertNotFound();
    }
}
