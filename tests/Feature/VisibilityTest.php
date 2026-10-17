<?php

namespace Tests\Feature;

use App\Models\Proverb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_returns_404(): void
    {
        $proverb = Proverb::factory()->create(['status' => 'draft']);
        $this->get(route('proverb.show', $proverb->slug))->assertNotFound();
    }

    public function test_pending_returns_404(): void
    {
        $proverb = Proverb::factory()->create(['status' => 'pending']);
        $this->get(route('proverb.show', $proverb->slug))->assertNotFound();
    }

    public function test_thin_approved_is_noindex(): void
    {
        $proverb = Proverb::factory()->approved()->create(['enrichment_score' => 1]);
        $response = $this->get(route('proverb.show', $proverb->slug));
        $response->assertOk();
        $response->assertSee('noindex', false);
    }
}
