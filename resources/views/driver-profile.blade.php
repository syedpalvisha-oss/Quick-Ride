<x-layouts.app title="Driver Profile">

<div class="p-10 relative max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Driver Profile</h1>

    <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl overflow-hidden">
        <!-- Header Strip -->
        <div class="h-32 bg-gradient-to-r from-indigo-900 to-purple-900 border-b border-gray-700 relative">
            <div class="absolute -bottom-12 left-8 w-24 h-24 bg-gray-900 rounded-full flex items-center justify-center border-4 border-gray-800 shadow-lg text-4xl">
                🚕
            </div>
        </div>

        <div class="pt-16 px-8 pb-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
                    <span class="bg-indigo-500/20 text-indigo-400 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-indigo-500/30 inline-block mt-2">Verified {{ ucfirst($user->role) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Personal Details -->
                <div class="space-y-4">
                    <div>
                        <span class="text-gray-500 block text-xs font-bold uppercase mb-1">Email Address</span>
                        <div class="text-white text-lg bg-gray-900/50 p-3 rounded-lg border border-gray-700/50">
                            {{ $user->email }}
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-xs font-bold uppercase mb-1">Phone Number</span>
                        <div class="text-white text-lg bg-gray-900/50 p-3 rounded-lg border border-gray-700/50">
                            {{ $user->phone ?? 'Not provided' }}
                        </div>
                    </div>
                </div>

                <!-- Platform Stats -->
                <div class="space-y-4">
                    <div>
                        <span class="text-gray-500 block text-xs font-bold uppercase mb-1">Member Since</span>
                        <div class="text-white text-lg bg-gray-900/50 p-3 rounded-lg border border-gray-700/50 flex items-center gap-2">
                            <span>📅</span> {{ $user->created_at->format('F j, Y') }}
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-xs font-bold uppercase mb-1">Lifetime Rides</span>
                        <div class="text-green-400 font-bold text-lg bg-gray-900/50 p-3 rounded-lg border border-gray-700/50 flex items-center gap-2">
                            <span>⭐</span> {{ $totalCompletedRides }} Completed
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

</x-layouts.app>
