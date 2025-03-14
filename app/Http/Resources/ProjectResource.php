<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'id'                  => $this->id,
            'name'                => $this->name,
            'short_code'          => $this->short_code,
            'users'               => UserResource::collection($this->users),
            'owners'              => UserResource::collection($this->users()->wherePivot('owner', true)->get()),
            'user_added_recently' => $this->resource->userAddedWithinLastHour($request->user()),
            'user_can_update'     => $request->user()->can('update', $this->resource),
            'invitations'         => InvitationResource::collection($this->invitations),
        ];

        foreach ($this->statuses as $status) {
            $data['statuses'][] = [
                'id'    => $status->id,
                'name'  => $status->name,
                'count' => $this->tasks()->where('status_id', $status->id)->count(),
            ];
        }

        return $data;
    }
}
