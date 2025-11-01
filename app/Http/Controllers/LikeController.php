<?php

namespace App\Http\Controllers;

use App\Http\Resources\LikeResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    private function getLikableType(string $type, int $id)
    {
        return match ($type) {
            'post' => Post::find($id),
            'comment' => Comment::find($id),
            default => null
        };
    }

    public function toggleLike($likeable_type, $likeable_id)
    {
        $likeable = $this->getLikableType($likeable_type, $likeable_id);
        if (! $likeable) {
            return $this->sendError('not found', 404);
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

        return $this->sendResponse([
            'liked' => $liked,
            'likes_count' => $likeable->likes()->count(),
        ], $liked ? 'Post liked' : 'Post unliked');
    }
    public function index()
    {
        $likes = Like::with(['user','likeable'])->get();
        return $this->sendResponse([
            'likes' => LikeResource::collection($likes),
        ], "likes list");
    }
}
