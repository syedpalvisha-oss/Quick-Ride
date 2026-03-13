<?php

namespace App\Models;

use App\Enums\VehicleType;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Concerns\HasUniqueIds;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    use HasUuids;

    protected function casts()
    {
        return [
            'pickup_location' => Point::class,
            'dropoff_location' => Point::class,
            'vehicle_type' => VehicleType::class,
            'matched_at' => 'datetime',
            'pickup_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'driver_cancelled_at' => 'datetime',
        ];
    }

    public static function booted()
    {
        static::creating(function (self $model) {
            $model->setUniqueIds();
        });
    }

    public function driver(){return $this->belongsTo(User::class, 'driver_id');}
    public function user(){return $this->belongsTo(User::class);}
    public function orderMessages(){return $this->hasMany(OrderMessage::class);}

    public function uniqueIds()
    {
        return ['uuid'];
    }
}
