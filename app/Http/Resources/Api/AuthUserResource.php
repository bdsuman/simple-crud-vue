<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id'      => $this->id,

            // Identity
            'role'        => $this->role,
            'full_name'  => $this->full_name,

            // Profile
            'gender'        => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'language'      => $this->language,
            'avatar'        => $this->avatar,

            // Contact & auth (no tokens exposed)
            'email'              => $this->email,

            // App state
            'status'                           => $this->status,

            // Timestamps
            'created_at' => $this->created_at,
        ];
    }
}
