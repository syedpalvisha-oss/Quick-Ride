<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePersonalAccessTokenRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreatePersonalAccessTokenController extends Controller
{
    /**
     * @unauthenticated
     */
    public function __invoke(CreatePersonalAccessTokenRequest $request)
    {
        $credentialField = $request->filled('email') ? 'email' : 'phone';

        $user = User::query()
            ->where($credentialField, $request->input($credentialField))
            ->withTrashed()
            ->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                $credentialField => __('auth.failed'),
            ]);
        }

        if ($user->trashed()) {
            $user->restore();
        }

        $token = $user->createToken('api');

        return response()->json([
            'data' => [
                'token' => $token->plainTextToken,
            ],
        ]);
    }
}
