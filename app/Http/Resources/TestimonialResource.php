<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'author_name'   => $this->author_name,
            'job_title'     => $this->job_title,
            'title'         => $this->title,
            'content'       => $this->content,
            'rating'        => $this->rating,
            'avatar'        => $this->avatar,
            'avatar_url'    => $this->avatar_url,
            'publish'       => $this->publish,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
