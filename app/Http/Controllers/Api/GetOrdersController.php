<?php

namespace App\Http\Controllers\Api;

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
