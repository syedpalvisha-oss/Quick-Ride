<?php

namespace App\Http\Controllers;

use App\Models\Ride;

class DriverDashboardController extends Controller
{
    public function index()
    {
        $availableRides = Ride::whereIn('status', ['pending', 'scheduled'])
            ->latest()
            ->get();

        $activeRides = Ride::where('driver_id', auth()->id())
            ->whereIn('status', ['accepted', 'arrived'])
            ->latest()
            ->get();

        $totalAcceptedRides = Ride::where('driver_id', auth()->id())->count();
        $totalCompletedRides = Ride::where('driver_id', auth()->id())->where('status', 'completed')->count();
        $completedToday = Ride::where('driver_id', auth()->id())
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->count();

        $recentCompletedRides = Ride::where('driver_id', auth()->id())
            ->where('status', 'completed')
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('driver-dashboard', compact('availableRides', 'activeRides', 'totalAcceptedRides', 'totalCompletedRides', 'completedToday', 'recentCompletedRides'));
    }

    public function accept($id)
    {
        $activeRideExists = Ride::where('driver_id', auth()->id())
            ->whereIn('status', ['accepted', 'arrived'])
            ->exists();

        if ($activeRideExists) {
            return back()->with('error', 'You already have an active ride.');
        }

        $ride = Ride::findOrFail($id);

        if (!in_array($ride->status, ['pending', 'scheduled']) || $ride->driver_id !== null) {
            return back()->with('error', 'Ride is no longer available.');
        }

        $ride->update([
            'status' => 'accepted',
            'driver_id' => auth()->id(),
        ]);

        return back()->with('success', 'Ride accepted successfully!');
    }

    public function arrived($id)
    {
        $ride = Ride::where('id', $id)
            ->where('driver_id', auth()->id())
            ->where('status', 'accepted')
            ->firstOrFail();

        $ride->update(['status' => 'arrived']);

        return back()->with('success', 'Ride marked as arrived!');
    }

    public function complete($id)
    {
        $ride = Ride::where('id', $id)
            ->where('driver_id', auth()->id())
            ->where('status', 'arrived')
            ->firstOrFail();

        $ride->update(['status' => 'completed']);

        return back()->with('success', 'Ride completed');
    }
}
