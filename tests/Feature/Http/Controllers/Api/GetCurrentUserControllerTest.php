<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('returns the authenticated user vehicles in the current user payload', function () {
    $user = User::factory()->create();
    $firstVehicle = $user->vehicles()->create([
        'code' => 'B 1234 JEK',
        'vehicle_type' => 0,
    ]);
    $secondVehicle = $user->vehicles()->create([
        'code' => 'B 9876 APP',
        'vehicle_type' => 1,
    ]);
    $user->forceFill([
        'vehicle_id' => $secondVehicle->getKey(),
    ])->save();

    Sanctum::actingAs($user);

    $this->getJson('/api/user')
        ->assertSuccessful()
        ->assertJsonPath('data.id', $user->getKey())
        ->assertJsonPath('data.vehicle_id', $secondVehicle->getKey())
        ->assertJsonPath('data.vehicle.id', $secondVehicle->getKey())
        ->assertJsonPath('data.is_driver', true)
        ->assertJsonPath('data.vehicles_count', 2)
        ->assertJsonCount(2, 'data.vehicles')
        ->assertJsonFragment([
            'id' => $firstVehicle->getKey(),
            'code' => 'B 1234 JEK',
            'vehicle_type' => 0,
        ])
        ->assertJsonFragment([
            'id' => $secondVehicle->getKey(),
            'code' => 'B 9876 APP',
            'vehicle_type' => 1,
        ]);
});
