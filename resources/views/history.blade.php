<x-layouts.app title="Ride History">

<div class="p-10 max-w-3xl mx-auto">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Your Rides</h1>
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition">
            &larr; Back to Dashboard
        </a>
    </div>

    @forelse($rides as $ride)

        <div class="bg-gray-800/80 p-6 mb-6 rounded-xl shadow-2xl border border-gray-700 hover:border-gray-500 transition duration-300">
            <!-- Header -->
            <div class="flex justify-between items-start mb-6 pb-4 border-b border-gray-700/50">
                <div>
                    <div class="text-gray-400 text-sm font-mono mb-1">
                        #{{ str_pad($ride->id, 5, '0', STR_PAD_LEFT) }} 
                        &bull; 
                        {{ $ride->created_at->format('M j, Y, h:i A') }}
                        @if($ride->ride_type === 'scheduled' && $ride->scheduled_time)
                            <span class="ml-2 text-indigo-400 block sm:inline mt-1 sm:mt-0">(Scheduled for {{ \Carbon\Carbon::parse($ride->scheduled_time)->format('M j, Y, h:i A') }})</span>
                        @endif
                    </div>
                    @if($ride->driver)
                        <div class="text-sm text-gray-300 mt-3 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center text-xs border border-gray-600">🚗</span>
                            Driver: <span class="text-white font-medium">{{ $ride->driver->name }}</span>
                        </div>
                    @endif
                </div>
                <div class="text-right flex flex-col items-end">
                    @if($ride->status === 'completed')
                        <span class="bg-green-500/20 text-green-400 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border border-green-500/30">Completed</span>
                    @elseif($ride->status === 'cancelled')
                        <span class="bg-red-500/20 text-red-400 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border border-red-500/30">Cancelled</span>
                    @elseif($ride->status === 'arrived')
                        <span class="bg-purple-500/20 text-purple-400 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border border-purple-500/30">Arrived</span>
                    @elseif($ride->status === 'accepted')
                        <span class="bg-blue-500/20 text-blue-400 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border border-blue-500/30">Accepted</span>
                    @else
                        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider border border-yellow-500/30">{{ ucfirst($ride->status) }}</span>
                    @endif
                    <div class="mt-3 text-2xl text-white font-bold">
                        ₹{{ number_format($ride->fare, 0) }}
                    </div>
                </div>
            </div>

            <!-- Route Info -->
            <div class="relative pl-6 mb-8 mt-4">
                <!-- Vertical dashed line connecting route points -->
                <div class="absolute left-[11px] top-4 bottom-4 w-0.5 flex flex-col justify-between">
                    <div class="h-full border-l-2 border-dashed border-gray-600"></div>
                </div>

                <!-- Pickup -->
                <div class="relative mb-6">
                    <div class="absolute -left-6 top-1.5 w-3.5 h-3.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)] border-2 border-gray-800 z-10"></div>
                    <div class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1">Pickup</div>
                    <div class="text-lg text-white font-medium">
                        {{ $ride->final_pickup_point ?? $ride->pickup }}
                    </div>
                </div>

                <!-- Stops -->
                @if($ride->stops && count($ride->stops) > 0)
                    @foreach($ride->stops as $index => $stop)
                        <div class="relative mb-6">
                            <div class="absolute -left-6 top-1.5 w-3h h-3 rounded-full bg-orange-400 shadow-[0_0_5px_rgba(251,146,60,0.6)] border-2 border-gray-800 z-10 ml-px mt-px" style="width: 12px; height: 12px; margin-left:1px; margin-top:1px;"></div>
                            <div class="text-xs text-orange-400/80 uppercase tracking-widest font-semibold mb-1">Stop {{ $index + 1 }}</div>
                            <div class="text-md text-gray-300 font-medium">
                                {{ $stop }}
                            </div>
                        </div>
                    @endforeach
                @endif

                <!-- Destination -->
                <div class="relative">
                    <div class="absolute -left-6 top-1.5 w-3.5 h-3.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)] border-2 border-gray-800 z-10"></div>
                    <div class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1">Drop Off</div>
                    <div class="text-lg text-white font-medium">
                        {{ $ride->destination }}
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-4 border-t border-gray-700/50 pt-5">
                <a href="{{ route('ride.show', $ride->id) }}" class="flex-1 bg-gray-700/50 hover:bg-gray-600 text-white text-center py-3 rounded-lg font-semibold transition duration-300 border border-gray-600 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 hidden sm:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    View Details
                </a>
                @php
                    $params = [
                        'pickup' => $ride->pickup,
                        'destination' => $ride->destination,
                    ];
                    if ($ride->stops) {
                        $params['stops'] = $ride->stops;
                    }
                @endphp
                <a href="{{ route('book.ride', $params) }}" class="flex-1 bg-green-500 hover:bg-green-600 text-white text-center py-3 rounded-lg font-bold transition duration-300 shadow-[0_0_15px_rgba(34,197,94,0.3)] flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 hidden sm:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Book Again
                </a>
            </div>
        </div>

    @empty

        <div class="bg-gray-800/50 p-12 rounded-2xl text-center border border-gray-700 shadow-2xl">
            <div class="text-7xl mb-6 drop-shadow-xl">🚖</div>
            <h2 class="text-3xl text-white font-bold mb-4">No rides yet</h2>
            <p class="text-gray-400 mb-8 max-w-md mx-auto text-lg leading-relaxed">You haven't booked any rides. Ready to embark on a quick, comfortable journey?</p>
            <a href="{{ route('book.ride') }}" class="inline-flex bg-green-500 hover:bg-green-600 text-white px-8 py-4 rounded-xl font-bold transition shadow-[0_0_20px_rgba(34,197,94,0.4)] text-lg border border-green-400/50">
                Book Your First Ride
            </a>
        </div>

    @endforelse

</div>

</x-layouts.app>