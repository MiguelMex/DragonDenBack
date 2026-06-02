<?php

namespace App\Http\Resources;

use App\Http\Resources\Profiles\ProfileResource;
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
            'id' => $this->user_id,
            'email' => $this->email,
            'profile' => new ProfileResource($this->whenLoaded('profileRelation')),
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at
        ];
    }
}
