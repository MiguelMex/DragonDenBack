<?php

namespace App\Http\Resources\Chapters;

use App\Http\Resources\DraftsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Rule;

class ChaptersDraftResource extends JsonResource
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
            'work' => $this->work,
            'created_at' => $this->created_at,
            'updated_at'=> $this->updated_at,
            'draft'=>new DraftsResource($this->whenLoaded('draftRelation')),
        ];
    }
}
