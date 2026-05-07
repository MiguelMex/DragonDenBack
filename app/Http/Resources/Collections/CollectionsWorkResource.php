<?php

namespace App\Http\Resources\Collections;

use App\Http\Resources\WorksResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionsWorkResource extends JsonResource
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
            'user' => $this->user,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'works'=>WorksResource::collection($this->whenLoaded('workRelation'))
        ];
    }
}
