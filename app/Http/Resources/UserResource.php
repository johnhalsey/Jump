<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'full_name'    => $this->full_name,
            'email'        => $this->email,
            'su'           => (bool)$this->super_admin,
            'gravatar_url' => $this->constructGravatarUrl()
        ];
    }

    private function constructGravatarUrl()
    {
        // Trim leading and trailing whitespace from
        // an email address and force all characters
        // to lower case
        $address = strtolower(trim($this->email));

        // Create an SHA256 hash of the final string
        $hash = hash('sha256', $address);

        // Grab the actual image URL
        return 'https://gravatar.com/avatar/' . $hash . '?d=robohash';
    }
}
