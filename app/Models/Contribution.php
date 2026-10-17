<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contribution extends Model
{
    protected $fillable = [
        'proverb_id', 'type', 'payload', 'player_id', 'status', 'ip_hash', 'license_ack', 'moderated_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'license_ack' => 'boolean',
        'moderated_at' => 'datetime',
    ];

    public function proverb(): BelongsTo
    {
        return $this->belongsTo(Proverb::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
