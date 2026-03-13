<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('issues token to the correct user when logging in by phone', function () {
    $wrongUser = User::factory()->create([
        'email' => null,
        'phone' => '+620000000001',
        'password' => 'password',
    ]);
    $targetUser = User::factory()->create([
        'email' => 'target-phone@example.test',
        'phone' => '+620000000002',
        'password' => 'password',
    ]);

    $this->postJson('/api/personal-access-tokens', [
        'phone' => '+620000000002',
        'password' => 'password',
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['data' => ['token']]);

    expect($targetUser->fresh()->tokens()->count())->toBe(1);
    expect($wrongUser->fresh()->tokens()->count())->toBe(0);
});

it('issues token to the correct user when logging in by email', function () {
    $wrongUser = User::factory()->create([
        'email' => 'wrong-email@example.test',
        'phone' => null,
        'password' => 'password',
    ]);
    $targetUser = User::factory()->create([
        'email' => 'target-email@example.test',
        'phone' => '+620000000099',
        'password' => 'password',
    ]);

    $this->postJson('/api/personal-access-tokens', [
        'email' => 'target-email@example.test',
        'password' => 'password',
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['data' => ['token']]);

    expect($targetUser->fresh()->tokens()->count())->toBe(1);
    expect($wrongUser->fresh()->tokens()->count())->toBe(0);
});
