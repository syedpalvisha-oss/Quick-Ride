<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class GetOrderController extends Controller
{
    /**
     *
     */
    public function __invoke(Order $order)
    {
        return new OrderResource(
            $order->load('user')->load('driver')
        );
    }
}
