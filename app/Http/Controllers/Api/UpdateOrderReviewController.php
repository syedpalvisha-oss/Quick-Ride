<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;

class UpdateOrderReviewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Order $order,
        Request $request,
        )
    {
        $order->update([
            'driver_rate' => $request->json('driver_rate'),
            'driver_review' => $request->json('driver_review'),
        ]);

        return new OrderResource($order);
    }
}
