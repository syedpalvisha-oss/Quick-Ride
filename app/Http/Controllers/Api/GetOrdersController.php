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
        $user = $request->user();
        $role = $request->string('role')->toString();
        $status = $request->string('status')->toString();
        $timezone = $request->header('X-Timezone');
        $fromDate = $request->query('from')
            ? $request->date(key: 'from', tz: $timezone)?->startOfDay()
            : null;
        $toDate = $request->query('to')
            ? $request->date(key: 'to', tz: $timezone)?->endOfDay()
            : null;

        $query = Order::query()
            ->when($fromDate, fn ($builder) => $builder->where('created_at', '>=', $fromDate))
            ->when($toDate, fn ($builder) => $builder->where('created_at', '<=', $toDate))
            ->when($request->enum('vehicle_type', VehicleType::class), fn ($builder, $value) => $builder->where('vehicle_type', $value))
            ->latest();

        if ($role === 'driver') {
            return OrderResource::collection(
                $query->with(['user', 'driver'])
                    ->where('driver_id', $user->getKey())
                    ->paginate()
            );
        }

        if ($role === 'driver_incoming') {
            $activeVehicle = $user->vehicle()->first();

            if (! $activeVehicle) {
                return OrderResource::collection(
                    $query->whereRaw('1 = 0')->paginate()
                );
            }

            $activeVehicleType = (int) ($activeVehicle->vehicle_type?->value ?? $activeVehicle->getRawOriginal('vehicle_type'));

            return OrderResource::collection(
                $query->with('user')
                    ->where('user_id', '!=', $user->getKey())
                    ->whereNull('driver_id')
                    ->whereNull('matched_at')
                    ->whereNull('pickup_at')
                    ->whereNull('completed_at')
                    ->whereNull('cancelled_at')
                    ->whereNull('driver_cancelled_at')
                    ->where('vehicle_type', $activeVehicleType)
                    ->paginate()
            );
        }

        return OrderResource::collection(
            $query
                ->when(
                    $user->isAdmin(),
                    fn ($builder) => $builder->with(['user', 'driver']),
                    fn ($builder) => $builder
                        ->with(['user', 'driver'])
                        ->where('user_id', $user->getKey())
                        ->when(
                            $status === 'active',
                            fn ($riderOrdersQuery) => $riderOrdersQuery
                                ->whereNull('completed_at')
                                ->whereNull('cancelled_at')
                                ->whereNull('driver_cancelled_at')
                        )
                        ->when(
                            $status === 'past',
                            fn ($riderOrdersQuery) => $riderOrdersQuery->where(function ($pastOrdersQuery) {
                                $pastOrdersQuery
                                    ->whereNotNull('completed_at')
                                    ->orWhereNotNull('cancelled_at')
                                    ->orWhereNotNull('driver_cancelled_at');
                            })
                        )
                )
                ->paginate()
        );
    }
}
