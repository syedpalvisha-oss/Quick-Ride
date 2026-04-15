<x-layouts.app title="Ride Details">

<div class="p-10 max-w-3xl mx-auto">

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
        <h1 class="text-3xl font-bold flex items-center gap-3">
            Ride Details
            @if(in_array($ride->status, ['pending', 'accepted', 'arrived']))
                <span class="text-xs font-normal text-green-400 bg-green-500/10 px-2 py-1 rounded-full animate-pulse border border-green-500/20">Live Syncing</span>
            @endif
        </h1>
        <a href="{{ route('history') }}" class="text-gray-400 hover:text-white transition whitespace-nowrap">
            &larr; Back to History
        </a>
    </div>

    <!-- Wrapping for Live Polling -->
    <div id="live_ride_card">
        <div class="bg-gray-800 rounded-xl shadow-2xl overflow-hidden border border-gray-700">
            
            <div class="p-8">
                <div class="flex flex-wrap justify-between items-start mb-6 pb-6 border-b border-gray-700 gap-4">
                    <div>
                        <span class="text-sm text-gray-400 mb-1 block">Ride ID</span>
                        <div class="text-lg font-mono text-gray-300">#{{ str_pad($ride->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-400 mb-1 block">Ride Type</span>
                        <div class="text-lg font-medium text-gray-300 capitalize flex items-center gap-2">
                            @if($ride->ride_type === 'scheduled')
                                <span class="text-blue-400">📅</span> Scheduled
                            @else
                                <span class="text-green-400">⚡</span> Instant
                            @endif
                        </div>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <span class="text-sm text-gray-400 block mb-2">Status</span>
                        @if($ride->status === 'completed')
                            <span class="bg-green-500/20 text-green-400 px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase border border-green-500/30">Completed</span>
                        @elseif($ride->status === 'cancelled')
                            <span class="bg-red-500/20 text-red-400 px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase border border-red-500/30">Cancelled</span>
                        @elseif($ride->status === 'arrived')
                            <span class="bg-purple-500/20 text-purple-400 px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase border border-purple-500/30">Arrived</span>
                        @elseif($ride->status === 'accepted')
                            <span class="bg-blue-500/20 text-blue-400 px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase border border-blue-500/30">Accepted</span>
                        @else
                            <span class="bg-yellow-500/20 text-yellow-400 px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase border border-yellow-500/30">{{ ucfirst($ride->status) }}</span>
                        @endif
                    </div>
                </div>

                <div class="mb-8 p-4 bg-gray-900/50 rounded-xl border border-gray-700/50">
                    <x-ride-progress :ride="$ride" />
                </div>

                @if($ride->driver)
                <div class="mb-6 p-5 bg-gray-700/50 rounded-xl border border-gray-600 flex justify-between items-center shadow-lg">
                    <div>
                        <span class="block text-sm text-gray-400 mb-1 uppercase tracking-wider font-semibold">Assigned Driver</span>
                        <span class="text-xl text-white font-bold">{{ $ride->driver->name }}</span>
                    </div>
                    <div class="text-4xl">🚕</div>
                </div>
                @elseif(in_array($ride->status, ['pending', 'scheduled']))
                <div class="mb-6 p-4 bg-yellow-500/5 rounded-xl border border-yellow-500/20 flex items-center justify-between">
                    <div>
                        <span class="block text-sm text-yellow-500/70 mb-1 font-semibold">Driver Status</span>
                        <span class="text-white">Searching for driver...</span>
                    </div>
                    <div class="text-2xl animate-spin text-yellow-600/50">↻</div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 mt-2">
                    <div>
                        <span class="block text-sm font-medium text-gray-400 mb-2 uppercase tracking-wider">Pickup Location</span>
                        <div class="text-lg text-white font-semibold flex items-start gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-green-500 inline-block shadow-[0_0_8px_rgba(34,197,94,0.6)] mt-1.5 shrink-0"></span>
                            <span class="leading-tight">{{ $ride->final_pickup_point ?? $ride->pickup }}</span>
                        </div>
                        @if($ride->pickup_radius)
                        <div class="text-xs text-gray-500 mt-2 ml-6">
                            Original request: {{ $ride->pickup }} (Radius: {{ $ride->pickup_radius }})
                        </div>
                        @endif
                    </div>
                    
                    <div>
                        <span class="block text-sm font-medium text-gray-400 mb-2 uppercase tracking-wider">Destination</span>
                        <div class="text-lg text-white font-semibold flex items-start gap-3">
                            <span class="w-3.5 h-3.5 rounded-full bg-red-500 inline-block shadow-[0_0_8px_rgba(239,68,68,0.6)] mt-1.5 shrink-0"></span>
                            <span class="leading-tight">{{ $ride->destination }}</span>
                        </div>
                    </div>

                    @if($ride->stops && count($ride->stops) > 0)
                        <div class="md:col-span-2 bg-gray-800/50 p-4 rounded-lg border border-gray-700/50">
                            <span class="block text-sm font-medium text-gray-400 mb-2 uppercase tracking-wider">Intermediate Stops</span>
                            <div class="space-y-2">
                                @foreach($ride->stops as $index => $stop)
                                <div class="text-base text-gray-200 font-medium flex items-start gap-3">
                                    <span class="w-2.5 h-2.5 rounded-full bg-orange-400 inline-block shadow-[0_0_5px_rgba(251,146,60,0.6)] mt-1.5 shrink-0 ml-0.5"></span>
                                    <span><span class="text-orange-400/80 mr-1 text-sm font-bold">{{ $index + 1 }}.</span> {{ $stop }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="bg-gray-800/30 p-4 rounded-lg border border-gray-700/30">
                        <span class="block text-sm font-medium text-gray-400 mb-1 uppercase tracking-wider">Estimated Fare</span>
                        <div class="text-3xl text-green-400 font-bold tracking-tight">
                            ₹{{ number_format($ride->fare, 0) }}
                        </div>
                    </div>

                    <div class="bg-gray-800/30 p-4 rounded-lg border border-gray-700/30">
                        <span class="block text-sm font-medium text-gray-400 mb-1 uppercase tracking-wider">Booked On</span>
                        <div class="text-lg text-gray-200 font-medium tracking-wide">
                            {{ $ride->created_at->format('M j, Y - g:i A') }}
                        </div>
                    </div>
                </div>

                @if($ride->status === 'pending')
                    <div class="mt-8 pt-8 border-t border-gray-700 flex justify-end">
                        <form method="POST" action="{{ route('ride.cancel', $ride->id) }}" onsubmit="if(confirm('Are you sure you want to cancel this ride?')) { this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Cancelling...'; return true; } return false;">
                            @csrf
                            <button type="submit" class="bg-transparent hover:bg-red-500/10 text-red-500 border border-red-500 font-bold py-3 px-8 rounded-lg transition duration-300 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed">
                                Cancel Ride
                            </button>
                        </form>
                    </div>
                @endif
                
                @if(in_array($ride->status, ['accepted', 'arrived']))
                    <div class="mt-8 pt-6 border-t border-gray-700 text-center text-sm text-gray-400 italic flex items-center justify-center gap-2">
                        <span class="animate-pulse">🟢</span> The driver is on their way. Enjoy your quick ride!
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>

<script>
    // Live polling check for updates on the active ride details
    @if(in_array($ride->status, ['pending', 'accepted', 'arrived', 'scheduled']))
    setInterval(() => {
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const currentContainer = document.getElementById('live_ride_card');
                const newContainer = doc.getElementById('live_ride_card');
                // Only replace if new HTML is different
                if (currentContainer && newContainer && currentContainer.innerHTML !== newContainer.innerHTML) {
                    currentContainer.innerHTML = newContainer.innerHTML;
                    
                    // Note: If status becomes completed/cancelled, polling continues 
                    // until user explicitly leaves page, but we could add logic to stop intervals
                    // if doc doesn't have the status string. But standard reload does the job nicely.
                }
            });
    }, 5000);
    @endif
</script>

</x-layouts.app>
