<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class GetCurrentUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        #[CurrentUser] User $user,
    ): UserResource {
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
