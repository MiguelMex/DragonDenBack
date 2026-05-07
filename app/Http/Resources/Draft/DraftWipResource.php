<?php

namespace App\Http\Resources\Draft;

use App\Http\Resources\WorksInProgressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DraftWipResource extends JsonResource
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
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'work_in_progress'=>new WorksInProgressResource($this->whenLoaded('wipRelation'))
        ];
    }
}
