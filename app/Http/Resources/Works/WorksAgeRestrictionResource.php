<?php

namespace App\Http\Resources\Works;

use App\Http\Resources\AgeRestrictionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorksAgeRestrictionResource extends JsonResource
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
            'work_in_progress'=>$this->work_in_progress,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'age_restriction'=> new AgeRestrictionsResource($this->whenLoaded('ageRestrictionRelation')),
        ];
    }
}
