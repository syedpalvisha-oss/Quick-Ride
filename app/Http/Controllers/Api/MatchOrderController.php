<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Validation\ValidationException;

class MatchOrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        Order $order,
        #[CurrentUser] User $user,
    ): OrderResource {
        $updated = Order::query()
            ->whereKey($order->getKey())
            ->whereNull('driver_id')
            ->whereNull('matched_at')
            ->whereNull('pickup_at')
            ->whereNull('completed_at')
            ->whereNull('cancelled_at')
            ->whereNull('driver_cancelled_at')
            ->update([
                'driver_id' => $user->getKey(),
                'matched_at' => now(),
            ]);

        if ($updated === 0) {
            throw ValidationException::withMessages([
                'order' => ['Order is no longer available for matching.'],
            ]);
        }

        return new OrderResource(
            $order->refresh()->load(['user', 'driver'])
        );
    }
}
