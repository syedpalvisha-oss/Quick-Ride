<x-layouts.app title="Welcome to QuickRide">

<div class="flex flex-col min-h-screen items-center justify-center p-4 relative">
    
    @if(session('error'))
        <div class="absolute top-10 w-full max-w-md bg-red-500 text-white font-bold px-6 py-4 rounded-lg shadow-2xl text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="text-center mb-12">
        <h1 class="text-5xl font-extrabold text-white mb-4">
            Quick<span class="text-green-400">Ride</span>
        </h1>
        <p class="text-gray-400 text-lg">Choose how you want to enter the platform</p>
    </div>
    
    <div class="flex flex-col md:flex-row gap-8 w-full max-w-3xl justify-center">
        <!-- User Entry -->
        <a href="/login?role=user" class="flex-1 bg-gray-900 p-12 rounded-2xl border border-gray-700 hover:border-green-500 hover:shadow-[0_0_30px_rgba(34,197,94,0.3)] hover:-translate-y-2 text-center transition-all duration-300 flex flex-col items-center group cursor-pointer">
            <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-6 group-hover:bg-green-500/20 transition-colors">
                <span class="text-5xl">👤</span>
            </div>
            <span class="text-2xl font-bold text-white mb-2">Continue as User</span>
            <span class="text-gray-500">Book rides and manage your trips</span>
        </a>

        <!-- Driver Entry -->
        <a href="/login?role=driver" class="flex-1 bg-gray-900 p-12 rounded-2xl border border-gray-700 hover:border-indigo-500 hover:shadow-[0_0_30px_rgba(99,102,241,0.3)] hover:-translate-y-2 text-center transition-all duration-300 flex flex-col items-center group cursor-pointer">
            <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-6 group-hover:bg-indigo-500/20 transition-colors">
                <span class="text-5xl">🚕</span>
            </div>
            <span class="text-2xl font-bold text-white mb-2">Continue as Driver</span>
            <span class="text-gray-500">Accept rides and start earning</span>
        </a>
    </div>

</div>

</x-layouts.app>