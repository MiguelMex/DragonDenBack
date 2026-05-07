<?php

namespace App\Http\Resources\Draft;

use App\Http\Resources\ChaptersResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DraftChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->draft_id,
            'name' => $this->name,
            'work_in_progress' => $this->work_in_progress,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'chapter'=>new ChaptersResource($this->whenLoaded('chapterRelation'))
        ];
    }
}
