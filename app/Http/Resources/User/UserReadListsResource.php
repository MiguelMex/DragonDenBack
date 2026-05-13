<?php

namespace App\Http\Resources\User;

use App\Http\Resources\ReadListsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserReadListsResource extends JsonResource
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
            'read_lists'=> ReadListsResource::collection($this->whenLoaded('readListsRelation')),
        ];
    }
}
