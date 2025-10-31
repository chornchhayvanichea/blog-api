<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
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
            'reason' => $this->reason,
            'reportable_type' => $this->reportable_type,
            'reportable_id' => $this->reportable_id,
            'reportable' => $this->whenLoaded('reportable'),
            'created_at' => $this->created_at,
        ];
    }
}
