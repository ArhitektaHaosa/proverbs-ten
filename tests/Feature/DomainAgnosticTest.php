<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainAgnosticTest extends TestCase
{
    use RefreshDatabase;

    public function test_canonical_uses_app_url(): void
    {
        config(['app.url' => 'https://example.test']);
        $response = $this->get('/');
        $response->assertSee('https://example.test', false);
    }
}
