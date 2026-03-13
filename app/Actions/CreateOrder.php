<?php

namespace App\Actions;

use App\Enums\VehicleType;
use App\Models\Order;
use App\Models\User;
use Clickbar\Magellan\Data\Geometries\Point;

class CreateOrder
{
    /**
     * Create a new class instance.
     */
    public function __invoke(
        Point $pickupLocation,
        Point $dropoffLocation,
        VehicleType $vehicleType,
        User $user
    )
    {
        return Order::create([
            'dropoff_location' => $dropoffLocation,
            'pickup_location' => $pickupLocation,
            'vehicle_type' => $vehicleType,
            'user_id' => $user->getKey(),
        ]);
    }
}
