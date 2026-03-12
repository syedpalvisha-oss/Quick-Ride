<?php

namespace App\Http\Controllers\Api;

use App\Enums\VehicleType;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;

class CreateOrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        CreateOrderRequest $request,
        #[CurrentUser] User $user,
    )
    {
        $order = Order::create([
            'dropoff_location' => $request->dropoffLocation(),
            'pickup_location' => $request->pickupLocation(),
            'vehicle_type' => $request->enum('vehicle_type', VehicleType::class),
            'user_id' => $user->getKey(),
        ]);

        return new OrderResource($order);
    }
}
