<?php

namespace App\Http\Resources\Mobile\Auth;

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
            'uuid'    => $this->uuid,

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
            'profile_setup_completed'          => (bool) $this->profile_setup_completed,
            'personality_profiling_completed'  => (bool) $this->personality_profiling_completed,
            'agreed_to_tos'                    => (bool) $this->agreed_to_tos,

            // Connected providers (booleans only; hides provider emails/IDs)
            'connected_providers' => [
                'google'   => (bool) $this->google_user_id,
                'facebook' => (bool) $this->facebook_user_id,
                'apple'    => (bool) $this->apple_user_id,
            ],

            // Timestamps
            'created_at' => $this->created_at,
        ];
    }
}
