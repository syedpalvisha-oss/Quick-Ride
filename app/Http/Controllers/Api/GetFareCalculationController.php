<?php

namespace App\Http\Controllers\Api;

use App\Actions\CalculateFare;
use App\Enums\Currency;
use App\Enums\VehicleType;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetFareCalculationRequest;
use Illuminate\Http\Request;

class GetFareCalculationController extends Controller
{
    public function __invoke(Request $request, CalculateFare $calculateFare)
    {
        return response()->json([
            'data' => $calculateFare(
                $request->json('pickup_location'),
                $request->json('dropoff_location'),
                $request->enum('vehicle_type', VehicleType::class),
                $request->enum('currency_id', Currency::class),
            ),
        ]);
    }
}
