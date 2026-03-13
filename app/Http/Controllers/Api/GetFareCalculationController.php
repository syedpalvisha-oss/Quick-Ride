<?php

namespace App\Http\Controllers\Api;

use App\Actions\CalculateFare;
use App\Enums\Currency;
use App\Enums\VehicleType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetFareCalculationController extends Controller
{
    public function __invoke(Request $request, CalculateFare $calculateFare)
    {
        return response()->json([
            'data' => $calculateFare(
                $request->input('pickup_location'),
                $request->input('dropoff_location'),
                $request->enum('vehicle_type', VehicleType::class),
                $request->enum('currency_id', Currency::class),
            ),
        ]);
    }
}
