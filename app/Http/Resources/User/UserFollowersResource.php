<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class UserFollowersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->profile_id,
            'nickname' => $this->nickname,
            'name' => $this->name,
            'description' => $this->description,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'followers'=> UserResource::collection($this->whenLoaded("followers")),
        ];
    }
}
