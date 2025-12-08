<?php

namespace App\Http\Resources\Api;

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
            'uuid'    => $this->uuid,

            // Identity
            'role'        => $this->role?->label(),
            'full_name'  => $this->full_name,

            // Profile
            'gender'        => $this->gender,
            'date_of_birth' => $this->date_of_birth, // keep as-is (YYYY-MM-DD if cast)
            'language'      => $this->language,
            'avatar'        => $this->avatar,
            'voice'         => $this->voice,
            'voice_url'     => $this->voice_url,
            'voice_title'   => $this->voice_title,
            'voice_status'  => $this->voice_status?->label(),
            'voice_cover_image_url'  => $this->voice_cover_image_url,
            'voice_description'  => $this->voice_description,

            // Cloned voice
            'cloned_voice'      => $this->cloned_voice,
            'cloned_voice_url'  => $this->cloned_voice_url,

            // Contact & auth (no tokens exposed)
            'email'              => $this->email,
            'is_email_verified'  => (bool) $this->email_verified_at,
            'login_type'         => $this->login_type,
            'last_login_at'      => $this->last_login_at,

            // App state
            'status'                           => $this->status?->label(),
            'profile_setup_completed'          => (bool) $this->profile_setup_completed,
            'personality_profiling_completed'  => (bool) $this->personality_profiling_completed,
            'agreed_to_tos'                    => (bool) $this->agreed_to_tos,

            // Connected providers (booleans only; hides provider emails/IDs)
            'connected_providers' => [
                'google'   => (bool) $this->google_user_id,
                'facebook' => (bool) $this->facebook_user_id,
                'apple'    => (bool) $this->apple_user_id,
            ],

            // Self-referencing relations (only when eager-loaded)
            'created_by' => $this->whenLoaded('createdBy', function () {
                return [
                    'id'         => $this->createdBy->id,
                    'full_name'  => $this->createdBy->full_name,
                    'email'      => $this->createdBy->email,
                ];
            }),
            'deleted_by' => $this->whenLoaded('deletedBy', function () {
                return [
                    'id'         => $this->deletedBy->id,
                    'full_name'  => $this->deletedBy->full_name,
                    'email'      => $this->deletedBy->email,
                ];
            }),

            // Timestamps
            'created_at' => $this->created_at,

            // Sessions
            'session_count' => $this->whenLoaded('sessions', function () {
                return $this->sessions->count();
            }),

            // Feedback
            'feedback_count' => $this->whenLoaded('feedbacks', function () {
                return $this->feedbacks->count();
            }),
        ];
    }
}
