<?php

namespace App\Http\Resources\History;

use App\Http\Resources\ChaptersResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadHistoryChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->read_history_id,
            'read_date'=>$this->read_date,
            'user'=> $this->user,
            'work'=> $this->work,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'chapter'=>new ChaptersResource($this->whenLoaded('chapterRelation'))
        ];
    }
}
