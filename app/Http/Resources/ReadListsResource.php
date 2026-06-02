<?php

namespace App\Http\Resources;

use App\Http\Resources\Profiles\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadListsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->read_list_id,
            'name'=>$this->name,
            'user'=> new ProfileResource($this->whenLoaded('profileRelation')),
            'description'=>$this->description,
            'works'=> WorksResource::collection($this->whenLoaded('workRelation')),
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
    }
}
