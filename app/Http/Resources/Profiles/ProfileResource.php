<?php

namespace App\Http\Resources\Profiles;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->profile_id,
            'name'=>$this->name,
            'nickname'=>$this->nickname,
            'description'=>$this->description,
            'avatar'=>$this->avatar,
            'works_count' => $this->works_count,
            'collections_count' => $this->collections_count,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
    }
}
