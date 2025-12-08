<?php

namespace App\Http\Resources\Mobile\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            // JSON casts
            'session_topics' => $this->session_topics ?? [],
            'session_topic_problem_levels' => $this->session_topic_problem_levels ?? [],

            // Voice info
            'voice_record_type' => $this->voice_record_type,
            'voice_record' => $this->voice_record,
        ];
    }
}
