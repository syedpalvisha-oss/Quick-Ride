<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('rejects switching to a vehicle that is not owned by the authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherVehicle = $otherUser->vehicles()->create([
        'code' => 'B 9999 OTH',
        'vehicle_type' => 0,
    ]);

    Sanctum::actingAs($user);

    $this->putJson('/api/users/mode', [
        'vehicle_id' => $otherVehicle->getKey(),
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['vehicle_id']);
});

it('assigns selected vehicle_id as active driver vehicle', function () {
    $user = User::factory()->create();
    $vehicle = $user->vehicles()->create([
        'code' => 'B 1234 XYZ',
        'vehicle_type' => 0,
    ]);

    Sanctum::actingAs($user);

    $this->putJson('/api/users/mode', [
        'vehicle_id' => $vehicle->getKey(),
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.vehicle_id', $vehicle->getKey())
        ->assertJsonPath('data.vehicle.id', $vehicle->getKey())
        ->assertJsonPath('data.is_driver', true)
        ->assertJsonPath('data.can_switch_to_driver_mode', true)
        ->assertJsonCount(1, 'data.vehicles');

    $this->assertDatabaseHas('driver_profiles', [
        'user_id' => $user->getKey(),
        'charges_enabled' => false,
        'payouts_enabled' => false,
    ]);
});

it('allows switching back to rider mode by setting vehicle_id to null', function () {
    $user = User::factory()->create();
    $vehicle = $user->vehicles()->create([
        'code' => 'B 7777 RID',
        'vehicle_type' => 0,
    ]);
    $user->forceFill([
        'vehicle_id' => $vehicle->getKey(),
    ])->save();

    Sanctum::actingAs($user);

    $this->putJson('/api/users/mode', [
        'vehicle_id' => null,
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.vehicle_id', null)
        ->assertJsonPath('data.is_driver', false);
});
