<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'id' => $this->id,
            'email' => $this->email,
            'avatar' => $this->avatar, // Add this
            'bio' => $this->bio, // Add this
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'role' => $this->role,
            'is_banned' => $this->is_banned,
            'posts' => $this->posts_count,
            'comments' => $this->comments_count,
            'receivedLikes_count' => $this->received_likes_count,
            'totalViews' => $this->posts->sum(function ($post) {
                return $post->viewers()->count();
            }),
            'profile' => new ProfileResource($this->whenLoaded('profile')),
        ];
    }
}
