<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $this->user->role,
                'profile' => [
                    'bio' => $this->user->profile?->bio,
                    'avatar' => $this->user->profile?->avatar
                        ? asset('storage/' . $this->user->profile->avatar)
                        : null,
                ],
            ],
            'content' => $this->content,
            'likes' => $this->likes->count() ?? 0,
            'is_liked' => $this->is_liked ?? false,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
