# QuickRide Project Source Code

### `routes/web.php`
```php
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ride;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Stripe\RefreshDriverStripeOnboardingController;
use App\Http\Controllers\Stripe\ReturnDriverStripeOnboardingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    return redirect('/login'); // better UX
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
        'password' => 'required|min:6'
    ]);

    $user = User::create([
        'name' => $request->name,
        'phone' => $request->phone,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    Auth::login($user);

    return redirect()->route('home');
});


// LOGIN
Route::post('/login', function (Request $request) {

    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();
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

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/home', function () {
        return view('home');
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
            'destination' => 'required',
            'ride_type' => 'required|in:instant,scheduled',
            'scheduled_time' => 'required_if:ride_type,scheduled|nullable|date|after:now',
        ]);

        $status = $request->ride_type === 'scheduled' ? 'scheduled' : 'pending';

        Ride::create([
            'user_id' => Auth::id(),
            'pickup' => $request->pickup,
            'destination' => $request->destination,
            'ride_type' => $request->ride_type,
            'scheduled_time' => $request->scheduled_time,
            'fare' => rand(100, 500),
            'status' => $status,
        ]);

        return redirect()->route('history');
    })->name('ride.store');


    // Ride History
    Route::get('/history', function () {

        $rides = Ride::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('history', compact('rides'));
    })->name('history');

    // Ride Details & Status
    Route::get('/ride/{id}', [\App\Http\Controllers\RideController::class, 'show'])->name('ride.show');
    Route::post('/ride/{id}/cancel', [\App\Http\Controllers\RideController::class, 'cancel'])->name('ride.cancel');
    Route::post('/ride/{id}/complete', [\App\Http\Controllers\RideController::class, 'complete'])->name('ride.complete');

});


// ================= STRIPE =================

Route::get('/stripe/connect/refresh/{driverProfile}', RefreshDriverStripeOnboardingController::class)
    ->middleware('signed')
    ->name('stripe.connect.refresh');

Route::get('/stripe/connect/return/{driverProfile}', ReturnDriverStripeOnboardingController::class)
    ->middleware('signed')
    ->name('stripe.connect.return');
```

### `routes/console.php`
```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\Ride;

Schedule::call(function () {
    Ride::where('status', 'scheduled')
        ->where('scheduled_time', '<=', now())
        ->update(['status' => 'pending']);
})->everyMinute();

```

### `app/Models/Ride.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pickup',
        'destination',
        'ride_type',
        'scheduled_time',
        'fare',
        'status',
    ];
}
```

### `app/Http/Controllers/RideController.php`
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        if ($ride->status === 'pending') {
            $ride->update(['status' => 'cancelled']);
        }

        return redirect()->route('ride.show', $ride->id)->with('status', 'cancelled');
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

        return redirect()->route('ride.show', $ride->id)->with('status', 'completed');
    }
}

```

### `resources/views/ride.blade.php`
```html
<x-layouts.app title="Book Ride">

<div class="p-10">

    <h1 class="text-3xl font-bold mb-8">
        Book a Ride
    </h1>

    <div class="max-w-xl">
        <form method="POST" action="/book-ride">
            @csrf

            <div class="mb-4">
                <label for="pickup" class="block text-sm font-medium text-gray-300">Pickup Location</label>
                <input type="text" name="pickup" id="pickup" required class="mt-1 block w-full rounded-md bg-gray-800 border-gray-600 text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-3">
            </div>

            <div class="mb-6">
                <label for="destination" class="block text-sm font-medium text-gray-300">Destination</label>
                <input type="text" name="destination" id="destination" required class="mt-1 block w-full rounded-md bg-gray-800 border-gray-600 text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-3">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-300 mb-2">Ride Type</label>
                <div class="flex space-x-4">
                    <label class="flex items-center text-gray-300 cursor-pointer">
                        <input type="radio" name="ride_type" value="instant" checked class="form-radio text-green-500 bg-gray-800 border-gray-600 focus:ring-green-500 mr-2" onchange="toggleSchedule()"> 
                        Book Now
                    </label>
                    <label class="flex items-center text-gray-300 cursor-pointer">
                        <input type="radio" name="ride_type" value="scheduled" class="form-radio text-green-500 bg-gray-800 border-gray-600 focus:ring-green-500 mr-2" onchange="toggleSchedule()"> 
                        Schedule Ride
                    </label>
                </div>
                @error('ride_type')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6 hidden" id="scheduled_time_container">
                <label for="scheduled_time" class="block text-sm font-medium text-gray-300">Scheduled Time</label>
                <input type="datetime-local" name="scheduled_time" id="scheduled_time" class="mt-1 block w-full rounded-md bg-gray-800 border-gray-600 text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-3" style="color-scheme: dark;">
                @error('scheduled_time')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="w-full bg-green-500 p-4 rounded text-center font-bold hover:bg-green-600 transition text-white">
                Confirm Booking
            </button>
            <a href="/home" class="block mt-4 text-center text-gray-400 hover:text-white transition">Cancel</a>
        </form>
        <script>
            function toggleSchedule() {
                const isScheduled = document.querySelector('input[name="ride_type"]:checked').value === 'scheduled';
                const container = document.getElementById('scheduled_time_container');
                const input = document.getElementById('scheduled_time');
                if (isScheduled) {
                    container.classList.remove('hidden');
                    input.required = true;
                } else {
                    container.classList.add('hidden');
                    input.required = false;
                    input.value = ''; // clear value
                }
            }
            
            // Re-run toggle to handle old input state if validation fails
            document.addEventListener('DOMContentLoaded', function() {
                toggleSchedule();
            });
        </script>
    </div>

</div>

</x-layouts.app>
```

### `resources/views/history.blade.php`
```html
<x-layouts.app title="Ride History">

<div class="p-10 max-w-2xl mx-auto">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Your Rides 🚗</h1>
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition">
            &larr; Back to Dashboard
        </a>
    </div>

    @forelse($rides as $ride)

        <a href="{{ route('ride.show', $ride->id) }}" class="block bg-gray-800 p-6 mb-4 rounded-lg hover:bg-gray-700 transition shadow-lg border border-gray-700 hover:border-green-500/50">
            <div class="flex justify-between items-start mb-4">
                <div class="text-gray-400 text-sm font-mono">
                    #{{ str_pad($ride->id, 5, '0', STR_PAD_LEFT) }} &bull; {{ $ride->created_at->format('M j, Y') }}
                </div>
                <div>
                    @if($ride->status === 'completed')
                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs font-semibold border border-green-500/30">Completed</span>
                    @elseif($ride->status === 'cancelled')
                        <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-xs font-semibold border border-red-500/30">Cancelled</span>
                    @else
                        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs font-semibold border border-yellow-500/30">Pending</span>
                    @endif
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div>
                    <div class="text-lg text-white font-semibold mb-1 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                        {{ $ride->pickup }}
                    </div>
                    <div class="text-lg text-white font-semibold flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                        {{ $ride->destination }}
                    </div>
                </div>
                <div class="text-xl text-green-400 font-bold">
                    ₹{{ number_format($ride->fare, 0) }}
                </div>
            </div>
        </a>

    @empty

        <div class="bg-gray-800 p-10 rounded-lg text-center border border-gray-700">
            <div class="text-4xl mb-4">🚖</div>
            <h2 class="text-xl text-white font-bold mb-2">No rides yet</h2>
            <p class="text-gray-400 mb-6">You haven't booked any rides. Ready to go?</p>
            <a href="{{ route('book.ride') }}" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded font-bold transition">
                Book a Ride
            </a>
        </div>

    @endforelse

</div>

</x-layouts.app>
```

### `resources/views/home.blade.php`
```html
<x-layouts.app title="Dashboard">

<div class="p-10">

    <h1 class="text-3xl font-bold mb-8">
        Welcome {{ Auth::user()->name }} 👋
    </h1>

    <div class="grid grid-cols-3 gap-6">

        <a href="/book-ride" class="bg-green-500 p-6 rounded text-center font-bold hover:scale-105 transition">
            🚗 Book Ride
        </a>

        <a href="/history" class="bg-yellow-400 text-black p-6 rounded text-center font-bold hover:scale-105 transition">
            📜 Ride History
        </a>

        <a href="/profile" class="bg-blue-500 p-6 rounded text-center font-bold hover:scale-105 transition">
            👤 Profile
        </a>

    </div>

</div>

</x-layouts.app>
```

### `database/migrations/2026_03_30_125752_create_rides_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pickup');
            $table->string('destination');
            $table->integer('fare')->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
```

### `database/migrations/2026_04_05_180321_add_scheduling_fields_to_rides_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->string('ride_type')->default('instant')->after('destination');
            $table->dateTime('scheduled_time')->nullable()->after('ride_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn(['ride_type', 'scheduled_time']);
        });
    }
};

```

