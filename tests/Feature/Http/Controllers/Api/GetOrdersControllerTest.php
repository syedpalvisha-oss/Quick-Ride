<?php

use App\Enums\VehicleType;
use App\Models\Order;
use App\Models\User;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function makeOrder(array $overrides = []): Order
{
    return Order::query()->create(array_merge([
        'user_id' => User::factory()->create()->getKey(),
        'vehicle_type' => VehicleType::MOTORBIKE,
        'pickup_location' => Point::makeGeodetic(-6.200000, 106.816666),
        'dropoff_location' => Point::makeGeodetic(-6.214620, 106.845130),
    ], $overrides));
}

function makeDriver(int $vehicleType = 0): User
{
    $driver = User::factory()->create();
    $vehicle = $driver->vehicles()->create([
        'code' => sprintf('B %04d DRV', random_int(1000, 9999)),
        'vehicle_type' => $vehicleType,
    ]);
    $driver->forceFill([
        'vehicle_id' => $vehicle->getKey(),
    ])->save();

    return $driver->fresh();
}

it('returns only orders associated to the authenticated driver', function () {
    $driver = makeDriver(VehicleType::MOTORBIKE->value);

    $rider = User::factory()->create();
    $otherDriver = makeDriver(VehicleType::MOTORBIKE->value);

    $assignedOrder = makeOrder([
        'user_id' => $rider->getKey(),
        'driver_id' => $driver->getKey(),
        'matched_at' => now(),
    ]);
    makeOrder([
        'driver_id' => $otherDriver->getKey(),
        'matched_at' => now(),
    ]);
    makeOrder([
        'user_id' => User::factory()->create()->getKey(),
    ]);

    Sanctum::actingAs($driver);

    $this->getJson('/api/orders?role=driver')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.uuid', $assignedOrder->uuid)
        ->assertJsonPath('data.0.user.id', $rider->getKey());
});

it('returns only incoming matchable orders compatible with the driver vehicles', function () {
    $driver = makeDriver(VehicleType::MOTORBIKE->value);

    $incomingOrder = makeOrder([
        'vehicle_type' => VehicleType::MOTORBIKE,
    ]);
    makeOrder([
        'vehicle_type' => VehicleType::CAR,
    ]);
    makeOrder([
        'driver_id' => User::factory()->create()->getKey(),
        'matched_at' => now(),
    ]);
    makeOrder([
        'cancelled_at' => now(),
    ]);
    makeOrder([
        'user_id' => $driver->getKey(),
    ]);

    Sanctum::actingAs($driver);

    $this->getJson('/api/orders?role=driver_incoming')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.uuid', $incomingOrder->uuid);
});

it('assigns driver when matching an incoming order and moves it to driver orders', function () {
    $driver = makeDriver(VehicleType::MOTORBIKE->value);

    $incomingOrder = makeOrder([
        'vehicle_type' => VehicleType::MOTORBIKE,
    ]);

    Sanctum::actingAs($driver);

    $this->postJson("/api/orders/{$incomingOrder->uuid}/match")
        ->assertSuccessful()
        ->assertJsonPath('data.uuid', $incomingOrder->uuid)
        ->assertJsonPath('data.driver.id', $driver->getKey());

    $incomingOrder->refresh();

    expect($incomingOrder->driver_id)->toBe($driver->getKey());
    expect($incomingOrder->matched_at)->not->toBeNull();

    $this->getJson('/api/orders?role=driver')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.uuid', $incomingOrder->uuid);
});

it('forbids matching incoming orders that do not match driver vehicle types', function () {
    $driver = makeDriver(VehicleType::MOTORBIKE->value);

    $carOrder = makeOrder([
        'vehicle_type' => VehicleType::CAR,
    ]);

    Sanctum::actingAs($driver);

    $this->postJson("/api/orders/{$carOrder->uuid}/match")
        ->assertForbidden();
});
