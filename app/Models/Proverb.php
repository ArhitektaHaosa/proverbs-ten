<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proverb extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'language_id', 'slug', 'text', 'text_normalized', 'text_hash',
        'literal_translation', 'meaning', 'usage_example', 'origin_note',
        'idea_id', 'status', 'source_type', 'source_url', 'source_title',
        'source_revision', 'license', 'attribution', 'enrichment_score',
        'ai_confidence', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'ai_confidence' => 'decimal:2',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }

    public function themes(): BelongsToMany
    {
        return $this->belongsToMany(Theme::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function isIndexable(): bool
    {
        return $this->status === 'approved'
            && $this->enrichment_score >= config('site.seo_min_enrichment_score');
    }
}
