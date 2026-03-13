<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateOrder;
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
        CreateOrder $createOrder,
    )
    {
        $order = $createOrder(
            $request->dropoffLocation(),
            $request->pickupLocation(),
            $request->enum('vehicle_type', VehicleType::class),
            $user,
        );

        return new OrderResource($order);
    }
}
