<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    protected $fillable = ['token_hash', 'nickname', 'recovery_code_hash', 'trusted', 'approved_count'];

    protected $casts = [
        'trusted' => 'boolean',
    ];

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function quizRuns(): HasMany
    {
        return $this->hasMany(QuizRun::class);
    }
}
