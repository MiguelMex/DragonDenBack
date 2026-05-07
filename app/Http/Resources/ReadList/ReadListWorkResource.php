<?php

namespace App\Http\Resources\ReadList;

use App\Http\Resources\WorksResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadListWorkResource extends JsonResource
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
            'user'=> $this->user,
            'description'=>$this->description,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'works'=>WorksResource::collection($this->whenLoaded('workRelation')),
        ];
    }
}
