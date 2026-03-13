<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use App\Services\StripeConnectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class HandleStripeConnectWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        StripeConnectService $stripeConnectService,
    ): JsonResponse {
        $payload = $request->getContent();
        $signature = (string) $request->header('Stripe-Signature');
        $webhookSecret = (string) config('services.stripe.webhook_secret');

        if ($webhookSecret === '') {
            return response()->json([
                'message' => 'Stripe webhook secret is not configured.',
            ], 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (UnexpectedValueException|SignatureVerificationException) {
            return response()->json([
                'message' => 'Invalid webhook payload or signature.',
            ], 400);
        }

        if ($event->type === 'account.updated') {
            $eventAccount = $event->data->object->toArray();
            $account = $stripeConnectService->normalizeAccount($eventAccount);

            $driverProfile = DriverProfile::query()
                ->where('stripe_account_id', $account['id'])
                ->first();

            if ($driverProfile) {
                $stripeConnectService->syncDriverProfileFromAccount($driverProfile, $account);
            }
        }

        return response()->json([
            'received' => true,
        ]);
    }
}
