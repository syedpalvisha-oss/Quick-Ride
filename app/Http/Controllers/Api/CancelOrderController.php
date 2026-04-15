<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;

class CancelOrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        #[CurrentUser] User $user,
        Order $order,
    ) {
        if ($order->user_id == $user->getKey()) {
            $order->touch('cancelled_at');
        } elseif ($order->driver_id == $user->getKey()) {
            $order->touch('driver_cancelled_at');
        }

        return new OrderResource($order);
    }
}
