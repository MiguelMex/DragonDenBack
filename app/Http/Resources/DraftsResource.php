<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DraftsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->draft_id,
            'name' => $this->name,
            'work_in_progress' => $this->work_in_progress,
            'route'=>$this->route,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at
        ];
    }
}
