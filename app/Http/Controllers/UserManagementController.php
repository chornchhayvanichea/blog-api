<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;

class UserManagementController extends Controller
{
    public function toggleBan(User $user)
    {
        $this->authorize('toggleBan', $user);

        $user->update([
            'is_banned' => ! $user->is_banned,
        ]);
        $user->refresh();

        return $this->sendResponse([
            'user' => new UserResource($user), // optionally wrap with UserResource if you have one
        ], $user->is_banned ? 'User banned' : 'User unbanned');
    }
}
