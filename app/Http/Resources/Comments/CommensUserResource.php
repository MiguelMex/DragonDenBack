<?php

namespace App\Http\Resources\Comments;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommensUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->comment_id,
            'text' => $this->text,
            'chapter' => $this->chapter,
            'response' => $this->response,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'user'=>new UserResource($this->whenLoaded('userRelation'))
        ];
    }
}
