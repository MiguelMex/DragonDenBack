<?php

namespace App\Http\Resources;

use App\Http\Resources\Profiles\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->work_id,
            'title'=>$this->title,
            'description'=>$this->description,
            'status'=>$this->status,
            'cover'=>$this->cover,
            'author'=> new ProfileResource($this->whenLoaded('authorRelation')),
            'age_restriction'=> new AgeRestrictionsResource($this->whenLoaded('ageRestrictionRelation')),
            'work_in_progress'=>$this->work_in_progress,
            'chapters'=>ChaptersResource::collection($this->whenLoaded('chapterRelation')),
            'chapters_count' => $this->chapters_count,
            'collections'=>CollectionsResource::collection($this->whenLoaded('collectionsRelation')),
            'lists'=> ReadListsResource::collection($this->whenLoaded('readListsRelation')),
            'genres'=>GenresResource::collection($this->whenLoaded('genresRelation')),
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }
}
