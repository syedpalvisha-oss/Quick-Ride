<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->getKey() === $order->driver_id
            || $user->getKey() === $order->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->orders()->whereNull('completed_at')->exists() === false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->getKey() === $order->driver_id
            || $user->getKey() === $order->user_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        return false;
    }

    public function cancel(User $user, Order $order)
    {
        if (! empty($order->cancelled_at)
            || ! empty($order->completed_at)
            || ! empty($order->driver_cancelled_at)
            || ! empty($order->pickup_at)) {
            return false;
        }
        if ($user->getKey() === $order->driver_id
            || $user->getKey() === $order->user_id) {
            return true;
        }

        return false;
    }

    public function match(User $user, Order $order)
    {
        if ($user->getKey() === $order->user_id) {
            return false;
        }

        if (empty($user->vehicle_id)) {
            return false;
        }

        $activeVehicle = $user->vehicle()->first();

        if (! $activeVehicle) {
            return false;
        }

        if (! empty($order->driver_id)
            || ! empty($order->matched_at)
            || ! empty($order->pickup_at)
            || ! empty($order->completed_at)
            || ! empty($order->cancelled_at)
            || ! empty($order->driver_cancelled_at)) {
            return false;
        }

        $orderVehicleType = (int) ($order->vehicle_type?->value ?? $order->getRawOriginal('vehicle_type'));
        $activeVehicleType = (int) ($activeVehicle->vehicle_type?->value ?? $activeVehicle->getRawOriginal('vehicle_type'));

        return $activeVehicleType === $orderVehicleType;
    }

    public function pickup(User $user, Order $order)
    {
        if ($user->getKey() === $order->driver_id) {
            return true;
        }

        return false;
    }

    public function complete(User $user, Order $order)
    {
        if ($user->getKey() === $order->driver_id) {
            return true;
        }

        return false;
    }

    public function review(User $user, Order $order)
    {
        return $order->user_id == $user->getKey();
    }
}
