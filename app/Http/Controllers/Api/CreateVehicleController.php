<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class CreateVehicleController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        CreateVehicleRequest $request,
        #[CurrentUser] User $user,
    ): VehicleResource {
        $vehicle = $user->vehicles()->create($request->validated());

        return new VehicleResource($vehicle);
    }
}
