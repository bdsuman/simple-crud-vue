<?php

namespace App\Models;

use App\Builders\TestimonialBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    /** @use HasFactory<\Database\Factories\TestimonialFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_name',
        'job_title',
        'title',
        'content',
        'rating',
        'avatar',
        'publish',
    ];

    protected $casts = [
        'publish'       => 'boolean',
        'deleted_at'    => 'datetime',
    ];
    
    public function newEloquentBuilder($query)
    {
        return new TestimonialBuilder($query);
    }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute()
    {
        if (is_null($this->avatar)) {
            return null;
        }

        if (preg_match('@http@', $this->avatar)) {
            return $this->avatar;
        }

        if (config('filesystems.default') == 'exoscale') {
            return Storage::disk('exoscale')->publicUrl($this->avatar);
        }

        return Storage::disk(config('filesystems.default'))->url($this->avatar);
    }
}
