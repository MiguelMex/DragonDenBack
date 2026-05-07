<?php

namespace App\Http\Resources\Comments;

use App\Http\Resources\ChaptersResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommensChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->comment_id,
            'text' => $this->text,
            'user' => $this->user,
            'response' => $this->response,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'chapter'=>new ChaptersResource($this->whenLoaded('chapterRelation')),
        ];
    }
}
