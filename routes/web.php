<?php

use App\Http\Controllers\DriverDashboardController;
use App\Http\Controllers\RideController;
use App\Http\Controllers\Stripe\RefreshDriverStripeOnboardingController;
use App\Http\Controllers\Stripe\ReturnDriverStripeOnboardingController;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    return view('welcome');
});

// ================= AUTH =================

// Show pages
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'))->name('register');

// REGISTER
Route::post('/register', function (Request $request) {

    $request->validate([
        'name' => 'required',
        'phone' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role' => 'required|in:user,driver',
    ]);

    $user = User::create([
        'name' => $request->name,
        'phone' => $request->phone,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
    ]);

    Auth::login($user);

    return redirect()->route($user->role === 'driver' ? 'driver.dashboard' : 'home');
});

// LOGIN
Route::post('/login', function (Request $request) {

    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'role' => 'required|in:user,driver',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();

        if (Auth::user()->role !== $request->role) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->with('error', 'Invalid login type. You are not a ' . $request->role . '.');
        }

        if (Auth::user()->role === 'driver') {
            return redirect()->route('driver.dashboard');
        }

        $userId = Auth::id();
        $reminder = null;
        $impendingRide = Ride::where('user_id', $userId)
            ->whereIn('status', ['pending', 'scheduled'])
            ->whereNotNull('scheduled_time')
            ->whereBetween('scheduled_time', [now(), now()->addMinutes(30)])
            ->orderBy('scheduled_time', 'asc')
            ->first();

        if ($impendingRide) {
            $diffInMinutes = round(now()->diffInMinutes($impendingRide->scheduled_time));
            $reminder = "Your ride is coming up in {$diffInMinutes} minutes!";
        } else {
            $recentInstantRide = Ride::where('user_id', $userId)
                ->where('status', 'pending')
                ->whereNull('scheduled_time')
                ->where('created_at', '>=', now()->subMinutes(30))
                ->first();
            if ($recentInstantRide) {
                $reminder = 'Your driver is arriving soon for your instant ride!';
            }
        }

        if ($reminder) {
            return redirect()->route('home')->with('login_reminder', $reminder);
        }

        return redirect()->route('home');
    }

    return back()->with('error', 'Invalid credentials');
});

// LOGOUT
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

// ================= PROTECTED ROUTES =================

Route::middleware(['auth', \App\Http\Middleware\IsUser::class])->group(function () {

    // Dashboard
    Route::get('/home', function () {

        $userId = Auth::id();
        $totalRides = Ride::where('user_id', $userId)->count();
        $completedRides = Ride::where('user_id', $userId)->where('status', 'completed')->count();
        $cancelledRides = Ride::where('user_id', $userId)->where('status', 'cancelled')->count();
        // Wait, upcoming rides logically should include accepted/arrived, but Active handles it now.
        $upcomingRides = Ride::where('user_id', $userId)->whereIn('status', ['pending', 'scheduled'])->count();

        $upcomingRideRecords = Ride::where('user_id', $userId)
            ->whereIn('status', ['pending', 'scheduled'])
            ->whereNotNull('scheduled_time')
            ->orderBy('status', 'desc') // Show accepted/etc first maybe, but pending/scheduled is fine
            ->orderBy('scheduled_time', 'asc')
            ->get();
            
        $activeRide = Ride::where('user_id', $userId)
            ->whereIn('status', ['pending', 'accepted', 'arrived'])
            ->latest()
            ->first();

        $reminder = null;
        $impendingRide = Ride::where('user_id', $userId)
            ->whereIn('status', ['pending', 'scheduled'])
            ->whereNotNull('scheduled_time')
            ->whereBetween('scheduled_time', [now(), now()->addMinutes(30)])
            ->orderBy('scheduled_time', 'asc')
            ->first();

        if ($impendingRide) {
            $diffInMinutes = round(now()->diffInMinutes($impendingRide->scheduled_time));
            $reminder = "Your ride is coming up in {$diffInMinutes} minutes!";
        } else {
            // Also check for instant rides recently booked
            $recentInstantRide = Ride::where('user_id', $userId)
                ->where('status', 'pending')
                ->whereNull('scheduled_time')
                ->where('created_at', '>=', now()->subMinutes(30))
                ->first();
            if ($recentInstantRide) {
                $reminder = 'Your driver is arriving soon for your instant ride!';
            }
        }

        return view('home', compact('totalRides', 'completedRides', 'cancelledRides', 'upcomingRides', 'upcomingRideRecords', 'activeRide', 'reminder'));
    })->name('home');

    // Profile
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // Book Ride Page
    Route::get('/book-ride', function () {
        return view('ride'); // matches your file
    })->name('book.ride');

    // Save Ride
    Route::post('/book-ride', function (Request $request) {

        $request->validate([
            'pickup' => 'required',
            'pickup_radius' => 'required|string',
            'final_pickup_point' => 'required|string',
            'destination' => 'required',
            'ride_type' => 'required|in:instant,scheduled',
            'scheduled_time' => 'required_if:ride_type,scheduled|nullable|date|after:now',
            'stops' => 'nullable|array|max:3',
            'stops.*' => 'nullable|string|max:255',
        ]);

        $status = $request->ride_type === 'scheduled' ? 'scheduled' : 'pending';

        $stopsArray = null;
        if ($request->has('stops') && is_array($request->stops)) {
            $stopsArray = array_values(array_filter($request->stops, fn ($val) => ! is_null($val) && trim($val) !== ''));
            $stopsArray = empty($stopsArray) ? null : $stopsArray;
        }

        Ride::create([
            'user_id' => Auth::id(),
            'pickup' => $request->pickup,
            'pickup_radius' => $request->pickup_radius,
            'final_pickup_point' => $request->final_pickup_point,
            'destination' => $request->destination,
            'ride_type' => $request->ride_type,
            'scheduled_time' => $request->scheduled_time,
            'stops' => $stopsArray,
            'fare' => rand(100, 500),
            'status' => $status,
        ]);

        return redirect()->route('history')->with('success', 'Ride booked successfully');
    })->name('ride.store');

    // Ride History
    Route::get('/history', function () {

        $rides = Ride::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('history', compact('rides'));
    })->name('history');

    // Ride Details & Status
    Route::get('/ride/{id}', [RideController::class, 'show'])->name('ride.show');
    Route::post('/ride/{id}/cancel', [RideController::class, 'cancel'])->name('ride.cancel');
    Route::post('/ride/{id}/complete', [RideController::class, 'complete'])->name('ride.complete');

});

// ================= DRIVER ROUTES =================

Route::middleware(['auth', 'driver'])->group(function () {
    Route::get('/driver/dashboard', [DriverDashboardController::class, 'index'])->name('driver.dashboard');
    
    Route::get('/driver/profile', function () {
        $user = Auth::user();
        $totalCompletedRides = \App\Models\Ride::where('driver_id', $user->id)->where('status', 'completed')->count();
        return view('driver-profile', compact('user', 'totalCompletedRides'));
    })->name('driver.profile');

    Route::post('/driver/ride/{id}/accept', [DriverDashboardController::class, 'accept'])->name('driver.ride.accept');
    Route::post('/driver/ride/{id}/arrived', [DriverDashboardController::class, 'arrived'])->name('driver.ride.arrived');
    Route::post('/driver/ride/{id}/complete', [DriverDashboardController::class, 'complete'])->name('driver.ride.complete');
});

// ================= STRIPE =================

Route::get('/stripe/connect/refresh/{driverProfile}', RefreshDriverStripeOnboardingController::class)
    ->middleware('signed')
    ->name('stripe.connect.refresh');

Route::get('/stripe/connect/return/{driverProfile}', ReturnDriverStripeOnboardingController::class)
    ->middleware('signed')
    ->name('stripe.connect.return');
