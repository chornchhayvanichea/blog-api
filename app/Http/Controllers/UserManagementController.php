<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserManagementController extends Controller
{
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('profile')
        ]);
    }

    public function index(User $user)
    {
        $this->authorize('index', $user);
        return response()->json([
            'users' => User::all()
        ]);
    }

    public function toggleBan(User $user)
    {
        $this->authorize('toggleBan', $user);
        $user->update([
            'is_banned' => !$user->is_banned
        ]);
        $user->refresh();
        return response()->json([
            'status' => 'success',
            'message' => $user->is_banned ? 'User banned' : 'User unbanned'
        ]);
    }
}
