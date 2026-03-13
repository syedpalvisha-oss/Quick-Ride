<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\StripeConnectService;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use LogicException;

class CreateDriverStripeOnboardingLinkController extends Controller
{
    public function __invoke(
        #[CurrentUser] User $user,
        StripeConnectService $stripeConnectService,
    ): JsonResponse {
        if (! $user->vehicles()->exists()) {
            throw ValidationException::withMessages([
                'vehicles' => ['Add at least one vehicle before starting Stripe onboarding.'],
            ]);
        }

        $driverProfile = $user->driverProfile()->firstOrCreate();

        try {
            if (! $driverProfile->stripe_account_id) {
                $account = $stripeConnectService->createExpressAccount($user);
            } else {
                $account = $stripeConnectService->retrieveAccount($driverProfile->stripe_account_id);
            }
        } catch (LogicException $exception) {
            throw ValidationException::withMessages([
                'stripe' => [$exception->getMessage()],
            ]);
        }

        $driverProfile = $stripeConnectService->syncDriverProfileFromAccount(
            $driverProfile,
            $account,
        );

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

        $onboardingUrl = $stripeConnectService->createAccountOnboardingLink(
            $driverProfile->stripe_account_id,
            $refreshUrl,
            $returnUrl,
        );

        return response()->json([
            'data' => [
                'url' => $onboardingUrl,
            ],
        ]);
    }
}
