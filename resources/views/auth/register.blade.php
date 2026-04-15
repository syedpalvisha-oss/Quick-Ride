@php
    $role = request('role', 'user');
    $isDriver = $role === 'driver';
    $textColor = $isDriver ? 'text-indigo-400' : 'text-green-400';
    $buttonColor = $isDriver ? 'bg-indigo-500 hover:bg-indigo-600 text-white' : 'bg-green-400 hover:bg-green-500 text-black';
    $focusBorder = $isDriver ? 'focus:border-indigo-400 focus:ring-indigo-400' : 'focus:border-green-400 focus:ring-green-400';
@endphp
<x-layouts.app title="Register as {{ ucfirst($role) }} — QuickRide">

<div class="flex min-h-screen items-center justify-center py-10">

    <div class="w-full max-w-md bg-gray-900 p-8 rounded border border-gray-700 shadow-xl relative">
        <a href="/login?role={{ $role }}" class="absolute -top-12 left-0 text-sm text-gray-400 hover:text-white transition">← Back to Login</a>

        <h2 class="text-2xl font-bold mb-2 text-center text-white">
            Quick<span class="{{ $textColor }}">Ride</span>
        </h2>
        <p class="text-gray-400 text-center mb-6 font-medium">Register as {{ ucfirst($role) }}</p>

        <form method="POST" action="/register">
            @csrf
            
            <input type="hidden" name="role" value="{{ $role }}">

            <input name="name" placeholder="Full Name" class="w-full p-3 mb-3 bg-gray-800 rounded border border-gray-700 text-white outline-none {{ $focusBorder }} transition" required>
            <input name="phone" placeholder="Phone Number" class="w-full p-3 mb-3 bg-gray-800 rounded border border-gray-700 text-white outline-none {{ $focusBorder }} transition" required>
            <input type="email" name="email" placeholder="Email Address" class="w-full p-3 mb-3 bg-gray-800 rounded border border-gray-700 text-white outline-none {{ $focusBorder }} transition" required>
            
            <input type="password" name="password" placeholder="Password" class="w-full p-3 mb-3 bg-gray-800 rounded border border-gray-700 text-white outline-none {{ $focusBorder }} transition" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full p-3 mb-6 bg-gray-800 rounded border border-gray-700 text-white outline-none {{ $focusBorder }} transition" required>

            <button class="w-full {{ $buttonColor }} py-3 rounded font-bold transition">
                Register as {{ ucfirst($role) }}
            </button>
        </form>

    </div>

</div>

</x-layouts.app>