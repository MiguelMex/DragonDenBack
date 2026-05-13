<?php

namespace App\Http\Resources\History;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReadHistoryUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->read_history_id,
            'read_date'=>$this->read_date,
            'work'=> $this->work,
            'chapter'=>$this->chapter,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
            'user'=>new ProfileResource($this->whenLoaded('userRelation'))
        ];
    }
}
