<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;

class LikeController extends Controller
{
    private function getLikableType(string $type, int $id)
    {
        return match($type) {
            'post' => Post::find($id),
            'comment' => Comment::find($id),
            default => null
        };
    }
    public function toggleLike($likeable_type, $likeable_id)
    {
        $likeable = $this->getLikableType($likeable_type, $likeable_id);
        if (!$likeable) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $user = auth()->user();

        $like = $likeable->likes()->firstWhere('user_id', $user->id);

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            $likeable->likes()->create(['user_id' => $user->id]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likeable->likes()->count()
        ]);
    }
}
