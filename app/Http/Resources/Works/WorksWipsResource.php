<?php

namespace App\Http\Resources\Works;

use App\Http\Resources\WorksInProgressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorksWipsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->work_id,
            'title'=>$this->title,
            'description'=>$this->description,
            'status'=>$this->status,
            'cover'=>$this->cover,
            'author'=>$this->author,
            'age_restriction'=>$this->age_restriction,
            'work_in_progress'=>$this->work_in_progress,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'wip'=>new WorksInProgressResource($this->whenLoaded('wipsRelation'))
        ];
    }
}
