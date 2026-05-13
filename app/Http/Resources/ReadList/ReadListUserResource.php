<?php

namespace App\Http\Resources\ReadList;

use App\Http\Resources\Profiles\ProfileResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadListUserResource extends JsonResource
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
            'description'=>$this->description,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'user'=>new ProfileResource($this->whenLoaded('userRelation')),
        ];
    }
}
