<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionsUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->collection_id,
            'name'=> $this->name,
            'description' => $this->description,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'user' => new UserResource($this->whenLoaded('userRelation'))
        ];
    }
}
