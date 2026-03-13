<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserModeRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class UpdateUserModeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        UpdateUserModeRequest $request,
        #[CurrentUser] User $user,
    ): UserResource {
        $vehicleId = $request->filled('vehicle_id')
            ? (int) $request->input('vehicle_id')
            : null;

        if ($vehicleId !== null) {
            $user->driverProfile()->firstOrCreate();
        }

        $user->forceFill([
            'vehicle_id' => $vehicleId,
        ])->save();

        return new UserResource(
            $user->loadCount('vehicles')
                ->load([
                    'driverProfile',
                    'vehicle',
                    'vehicles' => fn ($query) => $query->latest(),
                ])
        );
    }
}
