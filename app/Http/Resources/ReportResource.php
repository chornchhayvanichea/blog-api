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
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'reason' => $this->reason,
            'reportable_type' => $this->reportable_type,
            'reportable_id' => $this->reportable_id,
            'reportable' => $this->reportable,
            'reporter' => [
                'id' => $this->reporter->id,
                'name' => $this->reporter->name,
                'email' => $this->reporter->email,
                'avatar' => $this->reporter->profile?->avatar, // GET AVATAR FROM PROFILE
                'profile' => $this->reporter->profile, // OR include full profile
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status ?? 'pending',
        ];
    }
}
