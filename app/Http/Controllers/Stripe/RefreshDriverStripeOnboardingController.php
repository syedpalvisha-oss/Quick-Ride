<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use App\Services\StripeConnectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use LogicException;

class RefreshDriverStripeOnboardingController extends Controller
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

        $refreshUrl = URL::temporarySignedRoute(
            'stripe.connect.refresh',
            now()->addDays(7),
            ['driverProfile' => $driverProfile->getKey()],
        );

        $returnUrl = URL::temporarySignedRoute(
            'stripe.connect.return',
            now()->addDays(7),
            ['driverProfile' => $driverProfile->getKey()],
        );

        try {
            $url = $stripeConnectService->createAccountOnboardingLink(
                $driverProfile->stripe_account_id,
                $refreshUrl,
                $returnUrl,
            );
        } catch (LogicException) {
            return redirect()->route('home', [
                'stripe_connect' => 'configuration_error',
            ]);
        }

        return redirect()->away($url);
    }
}
