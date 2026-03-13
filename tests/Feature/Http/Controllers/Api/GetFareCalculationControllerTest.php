<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('it returns fare calculation breakdown for each supported currency', function () {
    Sanctum::actingAs(User::factory()->create());

    $query = http_build_query([
        'vehicle_type' => 0,
        'pickup_location' => [-6.21462, 106.84513],
        'dropoff_location' => [-6.17511, 106.86503],
    ]);

    $response = getJson("/api/calculate-fare?{$query}");

    $response
        ->assertSuccessful()
        ->assertJsonCount(8, 'data')
        ->assertJsonStructure([
            'data' => [[
                'vehicle_type',
                'currency_id',
                'distance_meters',
                'distance_km',
                'base_fare',
                'per_km_fare',
                'distance_fare',
                'booking_fee',
                'safety_fee',
                'minimum_fare',
                'minimum_fare_adjustment',
                'subtotal',
                'surge_multiplier',
                'surge_amount',
                'total_fare',
            ]],
        ]);

    $payload = $response->json('data');
    $currencies = collect($payload)
        ->pluck('currency_id')
        ->all();

    expect($payload)->toBeArray()
        ->and($currencies)->toBe([
            'EUR',
            'IDR',
            'MYR',
            'PHP',
            'SGD',
            'THB',
            'USD',
            'VND',
        ])
        ->and(collect($payload)->every(fn (array $fare): bool => $fare['vehicle_type'] === 0))->toBeTrue()
        ->and(collect($payload)->every(fn (array $fare): bool => (float) $fare['distance_meters'] > 0))->toBeTrue()
        ->and(collect($payload)->every(fn (array $fare): bool => (float) $fare['distance_km'] > 0))->toBeTrue()
        ->and(collect($payload)->every(fn (array $fare): bool => (float) $fare['total_fare'] > 0))->toBeTrue();
});

test('it can return fare calculation breakdown for a specific currency', function () {
    Sanctum::actingAs(User::factory()->create());

    $query = http_build_query([
        'vehicle_type' => 0,
        'pickup_location' => [-6.21462, 106.84513],
        'dropoff_location' => [-6.17511, 106.86503],
        'currency_id' => 'USD',
    ]);

    $response = getJson("/api/calculate-fare?{$query}");

    $response
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.currency_id', 'USD')
        ->assertJsonPath('data.0.vehicle_type', 0);

    $payload = $response->json('data.0');

    expect($payload)->toBeArray()
        ->and((float) $payload['distance_meters'])->toBeGreaterThan(0)
        ->and((float) $payload['distance_km'])->toBeGreaterThan(0)
        ->and((float) $payload['total_fare'])->toBeGreaterThan(0);
});

test('it returns fare calculation breakdown for all vehicle types when vehicle type is not provided', function () {
    Sanctum::actingAs(User::factory()->create());

    $query = http_build_query([
        'pickup_location' => [-6.21462, 106.84513],
        'dropoff_location' => [-6.17511, 106.86503],
    ]);

    $response = getJson("/api/calculate-fare?{$query}");

    $response
        ->assertSuccessful()
        ->assertJsonCount(16, 'data');

    $payload = collect($response->json('data'));
    $vehicleTypes = $payload
        ->pluck('vehicle_type')
        ->unique()
        ->sort()
        ->values()
        ->all();

    expect($vehicleTypes)->toBe([0, 1])
        ->and($payload->every(fn (array $fare): bool => (float) $fare['distance_meters'] > 0))->toBeTrue()
        ->and($payload->every(fn (array $fare): bool => (float) $fare['distance_km'] > 0))->toBeTrue()
        ->and($payload->every(fn (array $fare): bool => (float) $fare['total_fare'] > 0))->toBeTrue();
});
