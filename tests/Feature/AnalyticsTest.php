<?php

namespace Tests\Feature;

use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    public function test_ga4_absent_without_cmp(): void
    {
        config([
            'site.analytics_enabled' => true,
            'site.ga4_id' => 'G-TEST',
            'site.cmp_id' => null,
            'site.analytics_allow_without_cmp' => false,
        ]);
        $html = view('partials.analytics')->render();
        $this->assertStringNotContainsString('gtag', $html);
    }
}
