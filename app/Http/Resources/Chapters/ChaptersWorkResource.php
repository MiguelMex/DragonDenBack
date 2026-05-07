<?php

namespace App\Http\Resources\Chapters;

use App\Http\Resources\WorksResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChaptersWorkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->chapter_id,
            'name' => $this->name,
            'cover' => $this->cover,
            'route' => $this->route,
            'draft' => $this->draft,
            'created_at' => $this->created_at,
            'updated_at'=> $this->updated_at,
            'work'=> new WorksResource($this->whenLoaded('workRelation'))
        ];
    }
}
