<?php

namespace App\Services;

use App\Models\DriverProfile;
use App\Models\User;
use LogicException;
use Stripe\Account;
use Stripe\StripeClient;

class StripeConnectService
{
    protected function stripeClient(): StripeClient
    {
        $secret = (string) config('services.stripe.secret');

        if ($secret === '') {
            throw new LogicException('Stripe secret key is not configured.');
        }

        return new StripeClient($secret);
    }

    /**
     * @return array{
     *     id: string,
     *     details_submitted: bool,
     *     charges_enabled: bool,
     *     payouts_enabled: bool,
     *     requirements: array{
     *         currently_due: array<int, string>,
     *         eventually_due: array<int, string>,
     *         past_due: array<int, string>,
     *         pending_verification: array<int, string>,
     *         disabled_reason: ?string
     *     }
     * }
     */
    public function createExpressAccount(User $user): array
    {
        $account = $this->stripeClient()->accounts->create([
            'type' => 'express',
            'country' => (string) config('services.stripe.connect.country', 'US'),
            'email' => $user->email,
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
            'metadata' => [
                'user_id' => (string) $user->getKey(),
                'phone' => (string) $user->phone,
            ],
        ]);

        return $this->normalizeAccount($account);
    }

    /**
     * @return array{
     *     id: string,
     *     details_submitted: bool,
     *     charges_enabled: bool,
     *     payouts_enabled: bool,
     *     requirements: array{
     *         currently_due: array<int, string>,
     *         eventually_due: array<int, string>,
     *         past_due: array<int, string>,
     *         pending_verification: array<int, string>,
     *         disabled_reason: ?string
     *     }
     * }
     */
    public function retrieveAccount(string $stripeAccountId): array
    {
        $account = $this->stripeClient()->accounts->retrieve($stripeAccountId, []);

        return $this->normalizeAccount($account);
    }

    public function createAccountOnboardingLink(
        string $stripeAccountId,
        string $refreshUrl,
        string $returnUrl,
    ): string {
        $accountLink = $this->stripeClient()->accountLinks->create([
            'account' => $stripeAccountId,
            'type' => 'account_onboarding',
            'refresh_url' => $refreshUrl,
            'return_url' => $returnUrl,
        ]);

        return $accountLink->url;
    }

    /**
     * @param  array{
     *     id: string,
     *     details_submitted: bool,
     *     charges_enabled: bool,
     *     payouts_enabled: bool,
     *     requirements: array{
     *         currently_due: array<int, string>,
     *         eventually_due: array<int, string>,
     *         past_due: array<int, string>,
     *         pending_verification: array<int, string>,
     *         disabled_reason: ?string
     *     }
     * }  $account
     */
    public function syncDriverProfileFromAccount(DriverProfile $driverProfile, array $account): DriverProfile
    {
        $onboardingCompleted = $account['details_submitted']
            && $account['charges_enabled']
            && $account['payouts_enabled']
            && count($account['requirements']['currently_due']) === 0
            && count($account['requirements']['past_due']) === 0;

        $driverProfile->forceFill([
            'stripe_account_id' => $account['id'],
            'details_submitted' => $account['details_submitted'],
            'charges_enabled' => $account['charges_enabled'],
            'payouts_enabled' => $account['payouts_enabled'],
            'onboarding_completed_at' => $onboardingCompleted
                ? ($driverProfile->onboarding_completed_at ?? now())
                : null,
            'onboarding_requirements' => $account['requirements'],
        ])->save();

        return $driverProfile->refresh();
    }

    /**
     * @param  Account|array<string, mixed>  $account
     * @return array{
     *     id: string,
     *     details_submitted: bool,
     *     charges_enabled: bool,
     *     payouts_enabled: bool,
     *     requirements: array{
     *         currently_due: array<int, string>,
     *         eventually_due: array<int, string>,
     *         past_due: array<int, string>,
     *         pending_verification: array<int, string>,
     *         disabled_reason: ?string
     *     }
     * }
     */
    public function normalizeAccount(Account|array $account): array
    {
        $accountData = $account instanceof Account ? $account->toArray() : $account;
        $requirements = is_array($accountData['requirements'] ?? null)
            ? $accountData['requirements']
            : [];

        return [
            'id' => (string) ($accountData['id'] ?? ''),
            'details_submitted' => (bool) ($accountData['details_submitted'] ?? false),
            'charges_enabled' => (bool) ($accountData['charges_enabled'] ?? false),
            'payouts_enabled' => (bool) ($accountData['payouts_enabled'] ?? false),
            'requirements' => [
                'currently_due' => array_values($requirements['currently_due'] ?? []),
                'eventually_due' => array_values($requirements['eventually_due'] ?? []),
                'past_due' => array_values($requirements['past_due'] ?? []),
                'pending_verification' => array_values($requirements['pending_verification'] ?? []),
                'disabled_reason' => isset($requirements['disabled_reason'])
                    ? (string) $requirements['disabled_reason']
                    : null,
            ],
        ];
    }
}
