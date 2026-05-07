<?php

namespace App\Http\Resources\Comments;

use App\Http\Resources\CommentsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommensParentResource extends JsonResource
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
            'user' => $this->user,
            'response' => $this->response,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'parent'=>new CommentsResource($this->whenLoaded('parent'))
        ];
    }
}
