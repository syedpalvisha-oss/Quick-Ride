<?php

use App\Models\User;
use App\Services\StripeConnectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('rejects onboarding link creation when user has no vehicle', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->postJson('/api/driver/stripe/onboarding-link')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['vehicles']);
});

it('creates onboarding link and persists connect account id', function () {
    $user = User::factory()->create();
    $user->vehicles()->create([
        'code' => 'B 5555 JEK',
        'vehicle_type' => 0,
    ]);
    Sanctum::actingAs($user);

    app()->instance(StripeConnectService::class, new class extends StripeConnectService
    {
        public function createExpressAccount(User $user): array
        {
            return [
                'id' => 'acct_test_123',
                'details_submitted' => false,
                'charges_enabled' => false,
                'payouts_enabled' => false,
                'requirements' => [
                    'currently_due' => ['external_account'],
                    'eventually_due' => [],
                    'past_due' => [],
                    'pending_verification' => [],
                    'disabled_reason' => null,
                ],
            ];
        }

        public function createAccountOnboardingLink(string $stripeAccountId, string $refreshUrl, string $returnUrl): string
        {
            return 'https://connect.stripe.test/onboarding/acct_test_123';
        }
    });

    $this->postJson('/api/driver/stripe/onboarding-link')
        ->assertSuccessful()
        ->assertJsonPath('data.url', 'https://connect.stripe.test/onboarding/acct_test_123');

    $this->assertDatabaseHas('driver_profiles', [
        'user_id' => $user->getKey(),
        'stripe_account_id' => 'acct_test_123',
        'details_submitted' => false,
        'charges_enabled' => false,
        'payouts_enabled' => false,
    ]);
});

it('syncs driver profile from account.updated webhook', function () {
    $secret = 'whsec_test_secret';
    config()->set('services.stripe.webhook_secret', $secret);

    $user = User::factory()->create();
    $driverProfile = $user->driverProfile()->create([
        'stripe_account_id' => 'acct_sync_123',
    ]);

    $payload = json_encode([
        'id' => 'evt_123',
        'object' => 'event',
        'type' => 'account.updated',
        'data' => [
            'object' => [
                'id' => 'acct_sync_123',
                'object' => 'account',
                'details_submitted' => true,
                'charges_enabled' => true,
                'payouts_enabled' => true,
                'requirements' => [
                    'currently_due' => [],
                    'eventually_due' => [],
                    'past_due' => [],
                    'pending_verification' => [],
                    'disabled_reason' => null,
                ],
            ],
        ],
    ], JSON_THROW_ON_ERROR);

    $timestamp = time();
    $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $secret);
    $header = "t={$timestamp},v1={$signature}";

    $this->call(
        'POST',
        '/api/stripe/webhooks/connect',
        [],
        [],
        [],
        [
            'HTTP_STRIPE_SIGNATURE' => $header,
            'CONTENT_TYPE' => 'application/json',
        ],
        $payload,
    )->assertSuccessful();

    $driverProfile->refresh();

    expect($driverProfile->details_submitted)->toBeTrue();
    expect($driverProfile->charges_enabled)->toBeTrue();
    expect($driverProfile->payouts_enabled)->toBeTrue();
    expect($driverProfile->onboarding_completed_at)->not()->toBeNull();
});
