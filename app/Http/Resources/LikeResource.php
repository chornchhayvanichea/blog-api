<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'likeable_type' => $this->likeable_type,
            'likeable_id' => $this->likeable_id,
            'likeable' => $this->whenLoaded('likeable'),
            'created_at' => $this->created_at,

        ];
    }
}
