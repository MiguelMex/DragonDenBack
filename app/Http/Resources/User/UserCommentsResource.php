<?php

namespace App\Http\Resources\User;

use App\Http\Resources\CommentsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCommentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user_id,
            'nickname' => $this->nickname,
            'name' => $this->name,
            'email' => $this->email,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
            'comments'=> CommentsResource::collection($this->whenLoaded('commentsRelation')),
        ];
    }
}
