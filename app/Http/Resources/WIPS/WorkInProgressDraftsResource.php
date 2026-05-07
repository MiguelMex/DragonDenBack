<?php

namespace App\Http\Resources\WIPS;

use App\Http\Resources\DraftsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkInProgressDraftsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->work_in_progress_id,
            'title'=>$this->title,
            'author'=>$this->author,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'drafts'=>DraftsResource::collection($this->whenLoaded('draftsRelation')),
        ];
    }
}
