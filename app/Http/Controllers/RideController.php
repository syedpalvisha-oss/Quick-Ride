<?php

namespace App\Http\Controllers;

use App\Models\Ride;
use Illuminate\Support\Facades\Auth;

class RideController extends Controller
{
    /**
     * Show the ride details.
     */
    public function show($id)
    {
        $ride = Ride::where('user_id', Auth::id())->findOrFail($id);

        return view('ride.show', compact('ride'));
    }

    /**
     * Cancel a pending ride.
     */
    public function cancel($id)
    {
        $ride = Ride::where('user_id', Auth::id())->findOrFail($id);

        if (in_array($ride->status, ['pending', 'scheduled'])) {
            $ride->update(['status' => 'cancelled']);
        }

        return redirect()->back()->with('success', 'Ride cancelled');
    }

    /**
     * Mark a pending ride as completed.
     */
    public function complete($id)
    {
        $ride = Ride::where('user_id', Auth::id())->findOrFail($id);

        if ($ride->status === 'pending') {
            $ride->update(['status' => 'completed']);
        }

        return redirect()->back()->with('success', 'Ride completed');
    }
}
