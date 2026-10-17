<?php

namespace App\Policies;

use App\Models\Proverb;
use App\Models\User;

class ProverbPolicy
{
    public function view(?User $user, Proverb $proverb): bool
    {
        return $proverb->status === 'approved';
    }
}
