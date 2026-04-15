<?php

namespace App\Actions;

use App\Models\User;

class CreateUser
{
    /**
     * Create a new class instance.
     */
    public function __invoke(
        string $name,
        string $phone,
        ?string $email = null,
        ?string $password = null,
    ) {
        return User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
        ]);
    }
}
