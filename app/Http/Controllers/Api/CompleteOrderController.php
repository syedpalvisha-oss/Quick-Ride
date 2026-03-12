<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class CompleteOrderController extends Controller
{
    public function __invoke(Order $order, Request $request)
    {
        $order->update([
            'rate' => $request->json('rate'),
            'review' => $request->json('review'),
            'completed_at' => now(),
        ]);
        return new OrderResource($order);
    }
}
