<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePersonalAccessTokenRequest;
use App\Http\Resources\NewAccessTokenResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreatePersonalAccessTokenController extends Controller
{
    /**
     * @unauthenticated
     */
    public function __invoke(CreatePersonalAccessTokenRequest $request)
    {
        $user = User::query()
            ->where('email', $request->email)
            ->orWhere('phone', $request->phone)
            ->withTrashed()
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->trashed()) {
                $user->restore();
            }
            $token = $user->createToken('api');
            return [
                'data' => [
                    'token' => $token->plainTextToken,
                ],
            ];
        }
        throw ValidationException::withMessages([
            $request->filled('email') ? 'email' : 'phone' => __('auth.failed'),
        ]);
    }
}
