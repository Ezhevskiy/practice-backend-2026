<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'floor',
        'has_projector',
        'has_whiteboard',
        'has_video_conference',
        'has_monitor',
        'has_speakers',
        'price_per_hour',
        'is_active',
    ];

    protected $casts = [
        'has_projector'         => 'boolean',
        'has_whiteboard'        => 'boolean',
        'has_video_conference'  => 'boolean',
        'has_monitor'           => 'boolean',
        'has_speakers'          => 'boolean',
        'is_active'             => 'boolean',
        'price_per_hour'        => 'decimal:2',
    ];

    protected $appends = [
        'average_rating',
        'review_count',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasManyThrough(Review::class, Booking::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }
}