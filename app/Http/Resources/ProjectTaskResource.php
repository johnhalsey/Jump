<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'project'     => new ProjectResource($this->project),
            'title'       => $this->title,
            'description' => $this->description,
            'assignee'    => new UserResource($this->assignee),
            'creator_id'  => $this->creator_id,
            'reference'   => $this->reference,
            'status'      => $this->status,
            'notes'       => $this->when($this->resource->relationLoaded('notes'), TaskNoteResource::collection($this->notes->sortByDesc('created_at'))),
        ];
    }
}
