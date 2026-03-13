<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use App\Services\StripeConnectService;
use Illuminate\Http\RedirectResponse;
use LogicException;

class ReturnDriverStripeOnboardingController extends Controller
{
    public function __invoke(
        DriverProfile $driverProfile,
        StripeConnectService $stripeConnectService,
    ): RedirectResponse {
        if (! $driverProfile->stripe_account_id) {
            return redirect()->route('home', [
                'stripe_connect' => 'missing_account',
            ]);
        }

        try {
            $account = $stripeConnectService->retrieveAccount($driverProfile->stripe_account_id);
            $stripeConnectService->syncDriverProfileFromAccount($driverProfile, $account);
        } catch (LogicException) {
            return redirect()->route('home', [
                'stripe_connect' => 'configuration_error',
            ]);
        }

        return redirect()->route('home', [
            'stripe_connect' => 'return',
        ]);
    }
}
