<?php

namespace Tests\Feature;

use App\Services\Ai\DraftSchema;
use Tests\TestCase;

class AiSchemaTest extends TestCase
{
    public function test_valid_schema_passes(): void
    {
        $schema = new DraftSchema();
        $ok = $schema->validate([
            'meaning' => 'x',
            'literal_translation' => 'y',
            'english_gloss' => 'z',
            'themes' => [],
            'idea_label' => 'idea',
            'usage_example' => 'ex',
            'origin_note' => null,
            'confidence' => 0.8,
        ]);
        $this->assertTrue($ok);
    }

    public function test_invalid_schema_fails(): void
    {
        $schema = new DraftSchema();
        $this->assertFalse($schema->validate(['meaning' => 'only']));
    }
}
