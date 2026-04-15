<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'pickup',
        'pickup_radius',
        'final_pickup_point',
        'destination',
        'ride_type',
        'scheduled_time',
        'stops',
        'fare',
        'status',
        'pickup_lat',
        'pickup_lng',
        'drop_lat',
        'drop_lng',
        'distance',
    ];

    protected function casts(): array
    {
        return [
            'stops' => 'array',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
