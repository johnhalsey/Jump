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
        $data = [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'creator_id'  => $this->creator_id,
            'reference'   => $this->reference,
            'status'      => $this->status,
        ];

        if ($this->resource->relationLoaded('project')) {
            $data['project'] = new ProjectResource($this->project);
        }
        if ($this->resource->relationLoaded('assignee')) {
            $data['assignee'] = new UserResource($this->assignee);
        }
        if ($this->resource->relationLoaded('notes')) {
            $data['notes'] = TaskNoteResource::collection($this->notes);
        }

        return $data;
    }
}
