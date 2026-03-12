<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;

class DeletePersonalAccessTokenController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        #[CurrentUser] User $user,
    )
    {
        $user->currentAccessToken()->delete();
        return response()->noContent();
    }
}
