<?php

namespace App\Http\Resources\Genres;

use App\Http\Resources\WorksResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenreWorksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->genre_id,
            'name' => $this->name,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'works'=>WorksResource::collection($this->whenLoaded('workRelation')),
        ];
    }
}
