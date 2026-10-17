<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contribution_without_license_ack_fails(): void
    {
        $response = $this->post(route('contribute.store'), [
            'type' => 'meaning',
            'payload' => ['text' => 'test'],
        ]);
        $response->assertSessionHasErrors('license_ack');
    }
}
