<?php

namespace App\Http\Controllers\Api;

use App\Enums\VehicleType;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class GetOrdersController extends Controller
{
    public function __invoke(Request $request)
    {
        return OrderResource::collection(
            Order::query()
                ->when($request->query('from'), fn($query) => $query->where('created_at', '>=', $request->date(key: 'from', tz: $request->header('X-Timezone'))))
                ->when($request->query('to'), fn($query) => $query->where('created_at', '<=', $request->date(key: 'to', tz: $request->header('X-Timezone'))))
                ->when($request->enum('vehicle_type', VehicleType::class), fn($query, $value) => $query->where('vehicle_type', $value))
                ->when(
                    $request->query('role') === 'driver',
                    fn($query) => $query->with('driver')->where('driver_id', $request->user()->getKey()),
                    fn($query) => $query->when(
                        $request->user()->isAdmin(),
                        fn($query) => $query,
                        fn($query) => $query->with('user')->where('user_id', $request->user()->getKey())
                    )
                )
                ->paginate()
        );
    }
}
