<?php

namespace App\Http\Resources;

use App\Enums\ProjectPlan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ProjectStatus;
use Illuminate\Support\Facades\Log;
use App\Enums\DefaultProjectStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'                                               => $this->id,
            'name'                                             => $this->name,
            'short_code'                                       => $this->short_code,
            'user_can_update'                                  => $request->user()->can('update', $this->resource),
            'breadcrumb'                                       => [
                'route'   => route('project.show', $this->id),
                'display' => $this->name
            ],
            Str::lower(ProjectPlan::ADVANCED->value) . '_plan' => $this->planIs(ProjectPlan::ADVANCED)
        ];

        if ($this->resource->relationLoaded('users')) {
            $data['users'] = UserResource::collection($this->users);
        }
        if ($this->resource->relationLoaded('owners')) {
            $data['owners'] = UserResource::collection($this->owners);
        }
        if ($this->resource->relationLoaded('invitations')) {
            $data['invitations'] = InvitationResource::collection($this->invitations);
        }
        if ($this->resource->relationLoaded('statuses')) {
            $data['statuses'] = ProjectStatusResource::collection($this->statuses);
        }

        return $data;
    }
}
