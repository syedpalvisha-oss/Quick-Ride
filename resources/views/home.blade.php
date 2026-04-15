<x-layouts.app title="Dashboard">

<div class="p-10 relative">

    @if(isset($reminder) || session('login_reminder'))
    @php $displayReminder = $reminder ?? session('login_reminder'); @endphp
    <div id="reminderPopup" class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-4 z-50 animate-bounce">
        <span class="text-2xl">🔔</span>
        <div class="font-bold text-lg">
            {{ $displayReminder }}
        </div>
        <button onclick="document.getElementById('reminderPopup').style.display='none'" class="ml-4 text-white hover:text-gray-200 text-xl font-bold">&times;</button>
    </div>
    @endif

    <h1 class="text-3xl font-bold mb-8">
        Welcome {{ Auth::user()->name }} 👋
    </h1>

    <div class="mb-10" id="active_ride_section">
        <h2 class="text-2xl font-bold text-gray-200 mb-4">Current Ride</h2>
        @if($activeRide)
            <div class="bg-gray-800 p-6 rounded-lg border {{ $activeRide->status === 'pending' ? 'border-yellow-500/30 shadow-[0_0_15px_rgba(234,179,8,0.15)] hover:border-yellow-500/60' : 'border-purple-500/30 shadow-[0_0_15px_rgba(168,85,247,0.15)] hover:border-purple-500/60' }} transition">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-5 border-b border-gray-700/50 pb-5 gap-4">
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        @if($activeRide->status === 'pending')
                            <span class="text-3xl animate-spin text-yellow-500">↻</span>
                            <div class="flex-1">
                                <div class="text-sm text-gray-400 font-semibold uppercase tracking-wider mb-1">Status</div>
                                <div class="text-xl text-yellow-400 font-bold">Searching for driver...</div>
                            </div>
                        @else
                            <span class="text-3xl animate-bounce">🚕</span>
                            <div class="flex-1">
                                <div class="text-sm text-gray-400 font-semibold uppercase tracking-wider mb-1">Assigned Driver</div>
                                <div class="text-xl text-white font-bold truncate">{{ $activeRide->driver ? $activeRide->driver->name : 'Unknown' }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4 w-full">
                        <div class="flex flex-col items-center">
                            <div class="w-3.5 h-3.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                            <div class="h-8 w-0.5 border-l-2 border-dashed border-gray-600 my-1"></div>
                            <div class="w-3.5 h-3.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.6)]"></div>
                        </div>
                        <div class="flex flex-col justify-between h-[4.5rem] flex-1 overflow-hidden">
                            <div class="text-white font-medium truncate text-lg" title="{{ $activeRide->final_pickup_point ?? $activeRide->pickup }}">{{ $activeRide->final_pickup_point ?? $activeRide->pickup }}</div>
                            <div class="text-white font-medium truncate text-lg" title="{{ $activeRide->destination }}">{{ $activeRide->destination }}</div>
                        </div>
                    </div>
                    
                    <div class="hidden sm:block ml-4 shrink-0">
                        @if($activeRide->status === 'arrived')
                            <span class="bg-purple-500/20 text-purple-400 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border border-purple-500/30 animate-pulse text-center whitespace-nowrap">Driver has arrived</span>
                        @elseif($activeRide->status === 'accepted')
                            <span class="bg-blue-500/20 text-blue-400 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border border-blue-500/30 text-center flex items-center justify-center gap-2 whitespace-nowrap"><span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span> Driver is on the way</span>
                        @elseif($activeRide->status === 'pending')
                            <span class="bg-yellow-500/20 text-yellow-500 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border border-yellow-500/30 text-center whitespace-nowrap">Pending</span>
                        @endif
                    </div>
                </div>
                
                <div class="sm:hidden mb-5">
                    @if($activeRide->status === 'arrived')
                        <span class="bg-purple-500/20 text-purple-400 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border border-purple-500/30 animate-pulse block text-center">Driver has arrived</span>
                    @elseif($activeRide->status === 'accepted')
                        <span class="bg-blue-500/20 text-blue-400 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border border-blue-500/30 block text-center flex items-center justify-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span> Driver is on the way</span>
                    @elseif($activeRide->status === 'pending')
                        <span class="bg-yellow-500/20 text-yellow-500 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border border-yellow-500/30 block text-center">Pending</span>
                    @endif
                </div>
                
                <div class="mb-5 p-4 bg-gray-900/50 rounded-xl border border-gray-700/50">
                    <x-ride-progress :ride="$activeRide" />
                </div>
                
                
                <div class="pt-4 border-t border-gray-700/50 flex {{ $activeRide->status === 'pending' ? 'justify-between items-center' : 'justify-end' }}">
                    @if($activeRide->status === 'pending')
                        <form method="POST" action="{{ route('ride.cancel', $activeRide->id) }}" onsubmit="return confirm('Are you sure you want to cancel your ride request?');">
                            @csrf
                            <button type="submit" class="text-red-400 hover:text-red-300 font-semibold text-sm transition">Cancel Request</button>
                        </form>
                    @endif
                    <a href="{{ route('ride.show', $activeRide->id) }}" class="inline-block {{ $activeRide->status === 'pending' ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white font-bold py-3 px-6 rounded-lg transition duration-300 shadow shadow-black/20 text-center w-full sm:w-auto">View Ride Details</a>
                </div>
            </div>
        @else
            <div class="bg-gray-800 p-8 rounded-lg border border-gray-700 text-center shadow">
                <div class="text-4xl text-gray-600 mb-3 block">🛋️</div>
                <div class="text-xl text-gray-400 font-medium">No active ride</div>
            </div>
        @endif
    </div>

    <div class="mb-10">
        <h2 class="text-2xl font-bold text-gray-200 mb-4">Ride Insights</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="insights_container">
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center shadow hover:border-blue-500/50 transition">
                <div class="text-4xl font-bold text-white mb-2">{{ $totalRides }}</div>
                <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">Total Rides</div>
            </div>
            
            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center shadow hover:border-green-500/50 transition">
                <div class="text-4xl font-bold text-green-400 mb-2">{{ $completedRides }}</div>
                <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">Completed</div>
            </div>

            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center shadow hover:border-red-500/50 transition">
                <div class="text-4xl font-bold text-red-500 mb-2">{{ $cancelledRides }}</div>
                <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">Cancelled</div>
            </div>

            <div class="bg-gray-800 p-6 rounded-lg border border-gray-700 text-center shadow hover:border-yellow-500/50 transition">
                <div class="text-4xl font-bold text-yellow-500 mb-2">{{ $upcomingRides }}</div>
                <div class="text-gray-400 text-sm font-medium uppercase tracking-wider">Upcoming</div>
            </div>
        </div>
    </div>

    <h2 class="text-xl font-bold text-gray-300 mb-4 mt-10">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

        @if(Auth::user()->role !== 'driver')
        <a href="/book-ride" class="bg-green-500 hover:bg-green-600 text-white p-6 rounded-lg text-center font-bold shadow-lg transition transform hover:-translate-y-1">
            <div class="text-2xl mb-2">🚗</div>
            Book Ride
        </a>
        @endif

        <a href="/history" class="bg-yellow-500 hover:bg-yellow-600 text-black p-6 rounded-lg text-center font-bold shadow-lg transition transform hover:-translate-y-1">
            <div class="text-2xl mb-2">📜</div>
            Ride History
        </a>

        <a href="/profile" class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-lg text-center font-bold shadow-lg transition transform hover:-translate-y-1">
            <div class="text-2xl mb-2">👤</div>
            Profile
        </a>

        @if(Auth::user()->role === 'driver')
        <a href="{{ route('driver.dashboard') }}" class="bg-purple-500 hover:bg-purple-600 text-white p-6 rounded-lg text-center font-bold shadow-lg transition transform hover:-translate-y-1">
            <div class="text-2xl mb-2">🚕</div>
            Driver Dashboard
        </a>
        @endif

    </div>

    <h2 class="text-xl font-bold text-gray-300 mb-4 mt-6">Upcoming Rides <span class="ml-2 text-xs text-green-400 animate-pulse">(Live Updating)</span></h2>
    <div class="space-y-4" id="upcoming_rides_container">
        @if(isset($upcomingRideRecords) && $upcomingRideRecords->count() > 0)
            @foreach($upcomingRideRecords as $ride)
                <div onclick="window.location='{{ route('ride.show', $ride->id) }}'" class="cursor-pointer bg-gray-800 p-5 rounded-lg border border-gray-700 shadow flex flex-col md:flex-row justify-between items-start md:items-center hover:border-yellow-500/50 hover:bg-gray-700/50 transition group">
                    <div class="mb-4 md:mb-0 w-full">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-3 pb-3 border-b border-gray-700/50">
                            @if($ride->status === 'arrived')
                                <span class="bg-purple-500/20 text-purple-400 px-3 py-1 rounded-full text-xs font-semibold border border-purple-500/30 uppercase tracking-widest text-center">Arrived</span>
                            @elseif($ride->status === 'accepted')
                                <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-xs font-semibold border border-blue-500/30 uppercase tracking-widest text-center">Accepted</span>
                            @else
                                <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs font-semibold border border-yellow-500/30 uppercase tracking-widest text-center">{{ $ride->status }}</span>
                            @endif

                            @if($ride->scheduled_time)
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-200 font-medium">🕒 {{ \Carbon\Carbon::parse($ride->scheduled_time)->format('M j, Y - g:i A') }}</span>
                                    <span class="text-xs text-yellow-400 font-semibold mt-0.5 group-hover:text-yellow-300 transition">Starts {{ \Carbon\Carbon::parse($ride->scheduled_time)->diffForHumans() }}</span>
                                </div>
                            @else
                                <span class="text-sm text-gray-400">🕒 ASAP ({{ $ride->created_at->diffForHumans() }})</span>
                            @endif
                            
                            @if($ride->driver)
                            <div class="text-sm text-gray-300 flex items-center gap-2 sm:ml-auto bg-gray-700/50 px-3 py-1 rounded-full border border-gray-600">
                                <span class="w-4 h-4 text-xs">🚕</span>
                                <span class="text-white font-medium">{{ $ride->driver->name }}</span>
                            </div>
                            @else
                            <div class="text-sm text-gray-500 flex items-center gap-2 sm:ml-auto px-3 py-1">
                                <span class="animate-spin text-yellow-500/70">↻</span> Looking for driver...
                            </div>
                            @endif
                        </div>
                        <div class="text-white font-medium flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                            {{ $ride->final_pickup_point ?? $ride->pickup }}
                        </div>
                        <div class="text-white font-medium flex items-center gap-2 mt-2">
                            <span class="w-2 h-2 rounded-full bg-red-500 inline-block shadow-[0_0_8px_rgba(239,68,68,0.6)]"></span>
                            {{ $ride->destination }}
                        </div>
                    </div>
                    <div class="flex md:flex-col gap-2 w-full md:w-auto md:ml-6 shrink-0 mt-4 md:mt-0 relative z-10">
                        <a href="{{ route('ride.show', $ride->id) }}" onclick="event.stopPropagation()" class="flex-1 md:flex-none text-center text-sm bg-gray-700 text-white hover:bg-gray-600 px-4 py-2 rounded transition font-bold block">Manage Details</a>
                        @if(in_array($ride->status, ['pending', 'scheduled']))
                        <form method="POST" action="{{ route('ride.cancel', $ride->id) }}" onclick="event.stopPropagation()" onsubmit="return confirm('Are you sure you want to cancel this ride?');" class="flex-1 md:flex-none flex">
                            @csrf
                            <button type="submit" class="w-full text-sm bg-transparent hover:bg-red-500/10 text-red-500 border border-red-500 px-4 py-2 rounded transition font-bold">Cancel</button>
                        </form>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="bg-gray-800 p-8 rounded-lg border border-gray-700 text-center shadow">
                <div class="text-xl text-gray-400 font-medium">No upcoming rides scheduled</div>
            </div>
        @endif
    </div>

</div>

<script>
    // Live Polling for Up-To-Date Dashboard Data
    @if((isset($upcomingRideRecords) && $upcomingRideRecords->count() > 0) || isset($activeRide))
    setInterval(() => {
        fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const currentContainer = document.getElementById('upcoming_rides_container');
                const newContainer = doc.getElementById('upcoming_rides_container');
                if (currentContainer && newContainer && currentContainer.innerHTML !== newContainer.innerHTML) {
                    currentContainer.innerHTML = newContainer.innerHTML;
                }
                
                const currentInsights = document.getElementById('insights_container');
                const newInsights = doc.getElementById('insights_container');
                if (currentInsights && newInsights && currentInsights.innerHTML !== newInsights.innerHTML) {
                    currentInsights.innerHTML = newInsights.innerHTML;
                }

                const currentActiveRide = document.getElementById('active_ride_section');
                const newActiveRide = doc.getElementById('active_ride_section');
                if (currentActiveRide && newActiveRide && currentActiveRide.innerHTML !== newActiveRide.innerHTML) {
                    currentActiveRide.innerHTML = newActiveRide.innerHTML;
                }
            });
    }, 5000);
    @endif
</script>

</x-layouts.app>