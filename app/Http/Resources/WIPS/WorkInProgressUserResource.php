<?php

namespace App\Http\Resources\WIPS;

use App\Http\Resources\Profiles\ProfileResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkInProgressUserResource extends JsonResource
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
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'author'=>new ProfileResource($this->whenLoaded('userRelation'))
        ];
    }
}
