<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'user_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Средний рейтинг ресурса (можно использовать в Resource)
    public static function getAverageRatingForResource($resourceId)
    {
        return self::whereHas('booking', function ($q) use ($resourceId) {
            $q->where('resource_id', $resourceId);
        })->avg('rating') ?? 0;
    }
}