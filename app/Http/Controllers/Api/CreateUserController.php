<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class CreateUserController extends Controller
{
    /**
     * @unauthenticated
     */
    public function __invoke(CreateUserRequest $request)
    {
        $user = User::create($request->validated());
        return new UserResource($user);
    }
}
