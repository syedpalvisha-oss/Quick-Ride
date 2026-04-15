<x-layouts.app title="Driver Panel">

<div class="p-10 relative max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Driver Panel</h1>

    <!-- Driver Summary Section -->
    <div class="mb-8 bg-gray-900 border border-gray-800 rounded-lg p-5 flex flex-wrap gap-6 text-gray-300 text-sm shadow-sm tracking-wide">
        <div class="flex-1 min-w-[200px]">
            <strong class="text-white block mb-1">Total Lifetime Stats</strong>
            Accepted: {{ $totalAcceptedRides }} | Completed: {{ $totalCompletedRides }}
        </div>
        
        <div class="flex-1 min-w-[200px]">
            <strong class="text-white block mb-1">Today's Performance</strong>
            You have completed {{ $completedToday }} {{ Str::plural('ride', $completedToday) }} today.
        </div>

        <div class="flex-1 min-w-[200px]">
            <strong class="text-white block mb-1">Current Status</strong>
            @if($activeRides->count() > 0)
                <span class="text-blue-400 font-bold">On Active Ride (Ride #{{ $activeRides->first()->id }})</span>
            @else
                <span class="text-gray-500 font-bold">No active ride</span>
            @endif
        </div>
    </div>

    <!-- Current Mission Section -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-200 mb-6 border-b border-gray-800 pb-2">Current Mission</h2>
        @if($activeRides->count() > 0)
            <div class="space-y-6">
                @foreach($activeRides as $ride)
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30 shadow-[0_0_15px_rgba(234,179,8,0.3)]',
                            'accepted' => 'bg-blue-500/20 text-blue-400 border-blue-500/30 shadow-[0_0_20px_rgba(59,130,246,0.3)] animate-pulse',
                            'arrived' => 'bg-purple-500/20 text-purple-400 border-purple-500/30 shadow-[0_0_20px_rgba(168,85,247,0.3)] animate-pulse',
                            'completed' => 'bg-green-500/20 text-green-400 border-green-500/30',
                        ];
                        $colorClass = $statusColors[$ride->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                    @endphp
                    <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-8 rounded-2xl border border-gray-700 shadow-2xl relative overflow-hidden ring-1 ring-white/10 transform scale-[1.01] transition-all">
                        <!-- Dynamic Status Glow effect underlying the top corner -->
                        @if($ride->status === 'accepted')
                            <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-500/20 rounded-full blur-[50px]"></div>
                        @elseif($ride->status === 'arrived')
                            <div class="absolute -top-24 -right-24 w-48 h-48 bg-purple-500/20 rounded-full blur-[50px]"></div>
                        @endif

                        <div class="relative z-10 flex justify-between items-center mb-8">
                            <div>
                                <span class="text-gray-400 text-xs tracking-widest uppercase font-bold mb-1 block">Assigned Transport Mission</span>
                                <h3 class="text-3xl font-bold text-white tracking-tight">Ride #{{ $ride->id }}</h3>
                            </div>
                            <span class="{{ $colorClass }} px-6 py-2 rounded-full text-sm font-extrabold uppercase tracking-widest border border-white/20">{{ $ride->status }}</span>
                        </div>
                        
                        <div class="relative z-10 mb-8 p-6 bg-black/40 rounded-xl border border-gray-700/50 relative">
                            <!-- Visual progression timeline line -->
                            <div class="absolute left-9 top-10 bottom-10 w-0.5 {{ $ride->status === 'arrived' ? 'bg-purple-500/50' : 'bg-gray-700' }}"></div>

                            <div class="flex items-start gap-5 mb-8 relative">
                                <div class="w-6 h-6 rounded-full {{ $ride->status === 'arrived' ? 'bg-gray-600' : 'bg-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.6)]' }} flex items-center justify-center shrink-0 z-10 transition-colors">
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                                <div class="pt-0.5">
                                    <span class="font-bold text-gray-500 uppercase text-[10px] tracking-widest block mb-0.5">Pickup Location</span>
                                    <span class="text-xl text-white font-bold leading-none block">{{ $ride->pickup }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-5 relative">
                                <div class="w-6 h-6 rounded-full {{ $ride->status === 'arrived' ? 'bg-purple-500 shadow-[0_0_15px_rgba(168,85,247,0.6)]' : 'bg-gray-600' }} flex items-center justify-center shrink-0 z-10 transition-colors">
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                                <div class="pt-0.5">
                                    <span class="font-bold text-gray-500 uppercase text-[10px] tracking-widest block mb-0.5">Drop Destination</span>
                                    <span class="text-xl text-white font-bold leading-none block">{{ $ride->destination }}</span>
                                </div>
                        <div class="relative z-10 border-t border-gray-700/50 pt-6 mt-4">
                            @if($ride->status === 'accepted')
                                <p class="text-blue-400 text-sm font-bold text-center mb-4 flex items-center justify-center gap-2">
                                    <span class="animate-bounce">📍</span> Proceed to pickup location.
                                </p>
                                <form action="{{ route('driver.ride.arrived', $ride->id) }}" method="POST" onsubmit="const btn = this.querySelector('button'); btn.innerHTML='Locking Location...'; btn.classList.remove('hover:bg-blue-500'); btn.classList.add('opacity-50', 'pointer-events-none');">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 px-4 rounded-xl text-xl shadow-[0_4px_20px_rgba(37,99,235,0.4)] transition cursor-pointer flex items-center justify-center gap-2">
                                        Mark as Arrived
                                    </button>
                                </form>
                            @elseif($ride->status === 'arrived')
                                <p class="text-purple-400 text-sm font-bold text-center mb-4 flex items-center justify-center gap-2">
                                    <span class="animate-pulse">🟢</span> Objective secured. Transporting to the drop destination.
                                </p>
                                <form action="{{ route('driver.ride.complete', $ride->id) }}" method="POST" onsubmit="const btn = this.querySelector('button'); btn.innerHTML='Finalizing...'; btn.classList.remove('hover:bg-purple-500'); btn.classList.add('opacity-50', 'pointer-events-none');">
                                    @csrf
                                    <button type="submit" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-4 px-4 rounded-xl text-xl shadow-[0_4px_20px_rgba(147,51,234,0.4)] transition cursor-pointer flex items-center justify-center gap-2">
                                        Complete Mission
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-900/50 text-gray-400 px-6 py-10 rounded-xl border border-gray-800 border-dashed text-center">
                <div class="text-4xl mb-4 opacity-50">📡</div>
                <span class="block text-xl font-bold mb-2">No Current Mission</span>
                <span class="text-sm text-gray-500">Stand-by or select an available ride from the radar below.</span>
            </div>
        @endif
    </div>

    <!-- Available Rides Section -->
    <div>
        <h2 class="text-2xl font-bold text-gray-200 mb-6 border-b border-gray-800 pb-2">Available Rides</h2>
        @if($availableRides->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($availableRides as $ride)
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                            'accepted' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                            'arrived' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                            'completed' => 'bg-green-500/20 text-green-400 border-green-500/30',
                        ];
                        $colorClass = $statusColors[$ride->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                    @endphp
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 hover:border-gray-600 flex flex-col transition-colors">
                        <div class="flex-grow mb-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-widest block mb-1">New Ride Request</span>
                                    <h3 class="text-lg font-bold text-white">Ride #{{ $ride->id }}</h3>
                                </div>
                                <span class="{{ $colorClass }} px-2 py-1 border rounded text-xs font-bold uppercase tracking-wider">{{ $ride->status }}</span>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="bg-gray-900/50 p-3 rounded-lg border border-gray-700/50">
                                    <p class="text-sm text-gray-300 flex items-start gap-2 mb-3">
                                        <span class="mt-1 w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                                        <span class="flex-1 text-white">
                                            <span class="block text-xs text-gray-500 font-bold uppercase mb-0.5">Pickup</span>
                                            {{ $ride->pickup }}
                                        </span>
                                    </p>
                                    <p class="text-sm text-gray-300 flex items-start gap-2">
                                        <span class="mt-1 w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                                        <span class="flex-1 text-white">
                                            <span class="block text-xs text-gray-500 font-bold uppercase mb-0.5">Drop</span>
                                            {{ $ride->destination }}
                                        </span>
                                    </p>
                                </div>
                                <p class="text-sm text-gray-400 flex items-center gap-2">
                                    <span class="text-gray-500">🕒</span>
                                    {{ $ride->scheduled_time ? \Carbon\Carbon::parse($ride->scheduled_time)->format('M d, Y g:i A') : 'Instant / ASAP' }}
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('driver.ride.accept', $ride->id) }}" method="POST" class="mt-auto border-t border-gray-700 pt-4" onsubmit="const btn = this.querySelector('button'); btn.innerHTML='Accepting Ride...'; btn.classList.add('opacity-50', 'pointer-events-none', 'bg-gray-700', 'text-gray-400'); btn.classList.remove('bg-white', 'text-black', 'hover:bg-gray-200');">
                            @csrf
                            <button type="submit" class="w-full bg-white text-black hover:bg-gray-200 font-bold py-3 px-4 rounded-lg transition cursor-pointer">
                                Accept Ride
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-900/50 text-gray-400 px-6 py-12 rounded-xl border border-gray-800 border-dashed text-center flex flex-col items-center justify-center">
                <span class="text-4xl mb-4 opacity-50">📡</span>
                <span class="block text-xl font-bold text-gray-300 mb-2">No active ride requests</span>
                <span class="text-sm text-gray-500">Stay configured. Incoming ride requests in your sector will populate here instantly.</span>
            </div>
        @endif
    </div>

    <!-- Completed Rides Section -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-200 mb-6 border-b border-gray-800 pb-2">Recent Completed Rides</h2>
        @if($recentCompletedRides->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentCompletedRides as $ride)
                    <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 hover:border-gray-600 transition-colors opacity-90 hover:opacity-100 flex flex-col shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-xs font-bold text-green-500 uppercase tracking-widest block mb-1">Completed Task</span>
                                <h3 class="text-lg font-bold text-white">Ride #{{ $ride->id }}</h3>
                            </div>
                            <span class="bg-green-500/10 text-green-400 px-2 py-1 rounded text-xs font-bold border border-green-500/20">✔ {{ $ride->updated_at->diffForHumans() }}</span>
                        </div>
                        
                        <div class="space-y-4 flex-grow">
                            <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700/50">
                                <p class="text-sm text-gray-300 flex items-start gap-2 mb-3">
                                    <span class="mt-1 w-2 h-2 rounded-full bg-green-500 shrink-0 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                                    <span class="flex-1 text-white">
                                        <span class="block text-xs text-gray-500 font-bold uppercase mb-0.5 tracking-wider">Pickup</span>
                                        {{ $ride->pickup }}
                                    </span>
                                </p>
                                <p class="text-sm text-gray-300 flex items-start gap-2">
                                    <span class="mt-1 text-gray-400 text-[10px] shrink-0 block">🏁</span>
                                    <span class="flex-1 text-white">
                                        <span class="block text-xs text-gray-500 font-bold uppercase mb-0.5 tracking-wider">Drop</span>
                                        {{ $ride->destination }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-700/50 text-center">
                            <p class="text-xs text-gray-500 font-medium">
                                Completed: <span class="text-gray-400">{{ $ride->updated_at->format('M j, Y - g:i A') }}</span>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-900/50 text-gray-400 px-6 py-10 rounded-lg border border-gray-800 border-dashed text-center">
                <span class="block text-lg font-bold text-gray-300 mb-2">No completed rides yet</span>
                <span class="text-sm text-gray-500">When you complete a ride, it will beautifully document itself here.</span>
            </div>
        @endif
    </div>

</div>

</x-layouts.app>
