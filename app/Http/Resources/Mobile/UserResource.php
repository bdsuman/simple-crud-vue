<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'), // keep as-is (YYYY-MM-DD if cast)
            'language'      => $this->language,
            'avatar'        => $this->avatar,
            'avatar_url'    => $this->avatar_url,
            'voice'         => $this->voice,
            'voice_url'     => $this->voice_url,
            'voice_title'   => $this->voice_title,
            'voice_status'  => $this->voice_status,
            'voice_cover_image_url'  => $this->voice_cover_image_url,
            'voice_description'  => $this->voice_description,
            'eleven_lab_voice_id' => $this->eleven_lab_voice_id,
            'cloned_voice' => $this->cloned_voice,
            'cloned_voice_url' => $this->cloned_voice_url,
            'voice_text' => $this->voice_text,
            // Contact & auth (no tokens exposed)
            'email'              => $this->email,
            'is_email_verified'  => (bool) $this->email_verified_at,
            'login_type'         => $this->login_type,
            'last_login_at'      => $this->last_login_at,

            // App state
            'status'                           => $this->status,
            'profile_setup_completed'          => (bool) $this->profile_setup_completed,
            'personality_profiling_completed'  => (bool) $this->personality_profiling_completed,
            'agreed_to_tos'                    => (bool) $this->agreed_to_tos,
        ];
    }
}
