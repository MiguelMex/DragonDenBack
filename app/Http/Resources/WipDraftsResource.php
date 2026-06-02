<?php

namespace App\Http\Resources;

use App\Http\Resources\Profiles\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WipDraftsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->work_in_progress_id,
            'title'=>$this->resource->title,
            'author'=> new ProfileResource($this->whenLoaded('userRelation')),
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'drafts'=> DraftsResource::collection($this->whenLoaded('draftsRelation')),
        ];
    }
}
