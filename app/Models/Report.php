<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Report extends Model
{
    protected $fillable = ['reportable_type', 'reportable_id', 'reason', 'details', 'ip_hash', 'status'];

    public function reportable(): MorphTo
    {
        return $this->morphTo();
    }
}
