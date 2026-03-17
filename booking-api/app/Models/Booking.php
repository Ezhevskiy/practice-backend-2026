<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resource_id',
        'starts_at',
        'ends_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'status'    => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function hasConflict(): bool
    {
        return Booking::where('resource_id', $this->resource_id)
            ->where('id', '!=', $this->id ?? 0)
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('starts_at', '<', $this->ends_at)
                        ->where('ends_at', '>', $this->starts_at);
                });
            })
            ->exists();
    }
}