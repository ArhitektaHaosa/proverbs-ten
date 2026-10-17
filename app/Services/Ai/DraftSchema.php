<?php

namespace App\Services\Ai;

class DraftSchema
{
    public function validate(array $data): bool
    {
        $required = ['meaning', 'literal_translation', 'english_gloss', 'themes', 'idea_label', 'usage_example', 'origin_note', 'confidence'];

        foreach ($required as $key) {
            if (! array_key_exists($key, $data)) {
                return false;
            }
        }

        return is_numeric($data['confidence']) && $data['confidence'] >= 0 && $data['confidence'] <= 1;
    }
}
