<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiDraft extends Model
{
    protected $fillable = [
        'proverb_id', 'provider', 'model', 'prompt_version', 'input_hash', 'output', 'status', 'error', 'tokens',
    ];

    protected $casts = [
        'output' => 'array',
    ];

    public function proverb(): BelongsTo
    {
        return $this->belongsTo(Proverb::class);
    }
}
