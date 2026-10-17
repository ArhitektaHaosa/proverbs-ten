<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $fillable = [
        'code', 'name', 'native_name', 'script', 'direction', 'enabled', 'wiktionary_category', 'import_rank',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function proverbs(): HasMany
    {
        return $this->hasMany(Proverb::class);
    }
}
