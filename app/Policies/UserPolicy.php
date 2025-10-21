<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function toggleBan(User $authUser, User $targetUser): bool
    {
        return $authUser->role === 'admin';
    }
}
