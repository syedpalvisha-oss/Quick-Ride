<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('updates the authenticated user profile', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'phone' => '+628111111111',
        'email' => 'old@example.test',
    ]);

    Sanctum::actingAs($user);

    $this->putJson('/api/users', [
        'name' => 'New Name',
        'phone' => '+628222222222',
        'email' => 'new@example.test',
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.id', $user->getKey())
        ->assertJsonPath('data.name', 'New Name')
        ->assertJsonPath('data.phone', '+628222222222')
        ->assertJsonPath('data.email', 'new@example.test');

    $this->assertDatabaseHas('users', [
        'id' => $user->getKey(),
        'name' => 'New Name',
        'phone' => '+628222222222',
        'email' => 'new@example.test',
    ]);
});

it('validates phone and email uniqueness when updating profile', function () {
    $user = User::factory()->create([
        'phone' => '+628111111111',
        'email' => 'user@example.test',
    ]);
    User::factory()->create([
        'phone' => '+628999999999',
        'email' => 'taken@example.test',
    ]);

    Sanctum::actingAs($user);

    $this->putJson('/api/users', [
        'name' => 'Updated Name',
        'phone' => '+628999999999',
        'email' => 'taken@example.test',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['phone', 'email']);
});

it('allows keeping current phone and email on profile update', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'phone' => '+628777777777',
        'email' => 'keep@example.test',
    ]);

    Sanctum::actingAs($user);

    $this->putJson('/api/users', [
        'name' => 'Renamed User',
        'phone' => '+628777777777',
        'email' => 'keep@example.test',
    ])
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Renamed User')
        ->assertJsonPath('data.phone', '+628777777777')
        ->assertJsonPath('data.email', 'keep@example.test');
});
