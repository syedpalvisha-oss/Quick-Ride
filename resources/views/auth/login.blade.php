@php
    $role = request('role', 'user');
    $isDriver = $role === 'driver';
    $textColor = $isDriver ? 'text-indigo-400' : 'text-green-400';
    $buttonColor = $isDriver ? 'bg-indigo-500 hover:bg-indigo-600' : 'bg-green-400 hover:bg-green-500 text-black';
    $focusBorder = $isDriver ? 'focus:border-indigo-400 focus:ring-indigo-400' : 'focus:border-green-400 focus:ring-green-400';
@endphp
<x-layouts.app title="Login as {{ ucfirst($role) }} — QuickRide">

<div class="flex min-h-screen items-center justify-center">

    <div class="w-full max-w-md bg-gray-900 p-8 rounded border border-gray-700 shadow-xl relative">
        <a href="/" class="absolute -top-12 text-sm text-gray-400 hover:text-white transition">← Back to selection</a>

        <h2 class="text-2xl font-bold mb-2 text-center text-white">
            Quick<span class="{{ $textColor }}">Ride</span>
        </h2>
        <p class="text-gray-400 text-center mb-6 font-medium">Login as {{ ucfirst($role) }}</p>

        <form method="POST" action="/login">
            @csrf
            
            <input type="hidden" name="role" value="{{ $role }}">

            <input type="email" name="email" placeholder="Email"
                class="w-full p-3 mb-3 bg-gray-800 rounded border border-gray-700 text-white {{ $focusBorder }} outline-none transition" required>

            <input type="password" name="password" placeholder="Password"
                class="w-full p-3 mb-6 bg-gray-800 rounded border border-gray-700 text-white {{ $focusBorder }} outline-none transition" required>

            <button class="w-full {{ $buttonColor }} text-white py-3 rounded font-bold transition">
                Login as {{ ucfirst($role) }}
            </button>
        </form>

        <p class="mt-6 text-sm text-center text-gray-400">
            No account?
            <a href="/register?role={{ $role }}" class="{{ $textColor }} border-b border-transparent hover:border-current transition pb-0.5">Register as {{ ucfirst($role) }}</a>
        </p>

    </div>

</div>

</x-layouts.app>