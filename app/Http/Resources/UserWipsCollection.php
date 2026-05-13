<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\WorksInProgressResource;

class UserWipsCollection extends JsonResource
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
            'description' => $this->name,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'wips' => WorksInProgressResource::collection($this->whenLoaded('wipsRelation')),
        ];
    }
}
