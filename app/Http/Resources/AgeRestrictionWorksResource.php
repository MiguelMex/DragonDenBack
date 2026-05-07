<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\WorksResource;

class AgeRestrictionWorksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->age_restriction_id,
            "name"=> $this->name,
            "description"=> $this->description,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at,
            "works"=>WorksResource::collection($this->whenLoaded("workRelation")),
        ];
    }
}
