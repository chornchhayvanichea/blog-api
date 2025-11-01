<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth()->user();

        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'avatar' => $this->user->profile?->avatar ? asset('storage/' . $this->user->profile->avatar) : null,
            ],
            'category' => [
                'id' => $this->category->id,
                'category' => $this->category->name
            ],
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'status' => $this->status,
            'likes' => $this->likes->count(),
            'comments' => $this->comments->count(),
            'views' => $this->viewers->count(),
            'bookmarks' => $this->bookmark->count(),
            'is_liked' => $user ? $this->likedBy($user) : false,  // ← USE THIS
            'is_bookmarked' => $user ? $this->bookmark()->where('user_id', $user->id)->exists() : false,  // ← ADD THIS
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
