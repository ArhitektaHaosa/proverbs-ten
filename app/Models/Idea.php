<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Idea extends Model
{
    protected $fillable = ['slug', 'title', 'summary', 'status'];

    public function proverbs(): HasMany
    {
        return $this->hasMany(Proverb::class);
    }
}
