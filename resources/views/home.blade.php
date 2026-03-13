<x-layouts.app title="Dashboard — OpenJek">
    <x-slot:head>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    </x-slot:head>

    <div
        x-data="dashboard"
        class="flex h-screen flex-col"
    >
        {{-- Loading screen --}}
        <template x-if="loading">
            <div class="flex h-screen items-center justify-center bg-void-950">
                <div class="text-center">
                    <div class="mb-4 font-display text-2xl font-bold tracking-tight">
                        <span class="text-void-100">Open</span><span class="text-neon-400">Jek</span>
                    </div>
                    <div class="mx-auto h-1 w-24 overflow-hidden rounded-full bg-void-800">
                        <div class="animate-shimmer h-full w-full rounded-full bg-neon-400/40"></div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Main app --}}
        <template x-if="!loading">
            <div class="flex h-full flex-col">
                {{-- Top nav --}}
                <nav class="relative z-30 flex items-center justify-between border-b border-void-800/60 bg-void-950/90 px-4 py-3 backdrop-blur-xl">
                    <a href="/" class="flex items-center gap-1 font-display text-lg font-bold tracking-tight">
                        <span class="text-void-100">Open</span><span class="text-neon-400">Jek</span>
                    </a>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-void-300" x-text="user?.name"></span>
                        <a
                            href="/profile"
                            class="rounded-lg border border-void-700 px-3 py-1.5 text-xs font-medium text-void-300 transition hover:border-void-500 hover:text-void-100"
                        >Profile</a>
                        <div class="flex items-center rounded-lg border border-void-700 bg-void-900/70 p-1">
                            <button
                                x-on:click="switchToRider"
                                :disabled="switchModeLoading"
                                :class="!isDriverMode() ? 'bg-neon-400 text-void-950' : 'text-void-300 hover:text-void-100'"
                                class="rounded-md px-2.5 py-1 text-xs font-semibold transition disabled:cursor-not-allowed disabled:opacity-60"
                            >Rider</button>
                            <button
                                x-on:click="switchToDriver"
                                :disabled="switchModeLoading"
                                :class="isDriverMode() ? 'bg-neon-400 text-void-950' : 'text-void-300 hover:text-void-100'"
                                class="rounded-md px-2.5 py-1 text-xs font-semibold transition disabled:cursor-not-allowed disabled:opacity-60"
                            >Driver</button>
                        </div>
                        <button
                            x-on:click="logout"
                            class="rounded-lg border border-void-700 px-3 py-1.5 text-xs font-medium text-void-300 transition hover:border-void-500 hover:text-void-100"
                        >Logout</button>
                    </div>
                </nav>

                {{-- Map + Sidebar --}}
                <div class="relative flex flex-1 overflow-hidden">

                    {{-- ── Sidebar (desktop) / Bottom sheet (mobile) ── --}}
                    <div class="absolute inset-x-0 bottom-0 z-[800] max-h-[70vh] overflow-y-auto rounded-t-2xl border-t border-void-700/50 bg-void-950/95 p-5 backdrop-blur-xl md:static md:inset-auto md:z-auto md:max-h-none md:w-96 md:rounded-none md:border-r md:border-t-0 md:border-void-800/50">

                        {{-- ─ IDLE ─ --}}
                        <div x-show="step === 'idle' && !isDriverMode()" class="space-y-4">
                            <h2 class="font-display text-xl font-bold text-void-50">Where to?</h2>
                            <p class="text-sm text-void-300">Search an address or move the map to pick your location.</p>

                            <div>
                                <div class="relative">
                                    <div class="pointer-events-none absolute left-3 top-3">
                                        <div class="h-2.5 w-2.5 rounded-full bg-neon-400 shadow-[0_0_8px_rgba(200,255,0,0.5)]"></div>
                                    </div>
                                    <input
                                        type="text"
                                        x-model="pickupSearch"
                                        x-on:input="onSearchInput(pickupSearch)"
                                        x-on:focus="if (searchResults.length) showResults = true"
                                        placeholder="Search pickup location..."
                                        class="w-full rounded-lg border border-void-600 bg-void-800 py-2.5 pl-9 pr-4 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                                    >
                                </div>

                                {{-- Inline search results --}}
                                <div x-show="showResults" x-transition class="mt-1 divide-y divide-void-700/50 rounded-lg border border-void-600 bg-void-800">
                                    <template x-for="(result, i) in searchResults" :key="i">
                                        <button
                                            x-on:click="selectSearchResult(result)"
                                            class="flex w-full items-start gap-2.5 px-3 py-2.5 text-left text-sm transition hover:bg-void-700"
                                        >
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-void-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                            <span class="line-clamp-2 text-void-100" x-text="result.display_name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <button
                                x-show="!showResults"
                                x-on:click="startPickupSelection"
                                class="w-full rounded-lg bg-neon-400/10 py-3 text-sm font-medium text-neon-400 transition hover:bg-neon-400/20"
                            >
                                Choose on map
                            </button>

                            <div class="space-y-2 rounded-lg border border-void-700 bg-void-800/30 p-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-medium uppercase tracking-wide text-void-400">My Orders</p>
                                    <button
                                        x-on:click="fetchRiderOrders"
                                        class="text-xs font-medium text-neon-400 transition hover:text-neon-300"
                                    >Refresh</button>
                                </div>

                                <div class="flex items-center rounded-lg border border-void-700 bg-void-900/60 p-1">
                                    <button
                                        x-on:click="setRiderOrdersTab('active')"
                                        :class="riderOrdersTabClass('active')"
                                        class="flex-1 rounded-md px-2 py-1.5 text-xs font-semibold transition"
                                    >Active</button>
                                    <button
                                        x-on:click="setRiderOrdersTab('past')"
                                        :class="riderOrdersTabClass('past')"
                                        class="flex-1 rounded-md px-2 py-1.5 text-xs font-semibold transition"
                                    >Past</button>
                                    <button
                                        x-on:click="setRiderOrdersTab('date_range')"
                                        :class="riderOrdersTabClass('date_range')"
                                        class="flex-1 rounded-md px-2 py-1.5 text-xs font-semibold transition"
                                    >Date Range</button>
                                </div>

                                <div x-show="riderOrdersTab === 'date_range'" class="space-y-2">
                                    <div class="grid grid-cols-2 gap-2">
                                        <label class="space-y-1">
                                            <span class="text-[11px] text-void-400">From</span>
                                            <input
                                                type="date"
                                                x-model="riderOrdersFrom"
                                                class="w-full rounded-md border border-void-600 bg-void-900 px-2.5 py-1.5 text-xs text-void-100 focus:border-neon-400/50 focus:outline-none focus:ring-1 focus:ring-neon-400/30"
                                            >
                                        </label>
                                        <label class="space-y-1">
                                            <span class="text-[11px] text-void-400">To</span>
                                            <input
                                                type="date"
                                                x-model="riderOrdersTo"
                                                class="w-full rounded-md border border-void-600 bg-void-900 px-2.5 py-1.5 text-xs text-void-100 focus:border-neon-400/50 focus:outline-none focus:ring-1 focus:ring-neon-400/30"
                                            >
                                        </label>
                                    </div>

                                    <div class="flex gap-2">
                                        <button
                                            x-on:click="applyRiderDateRange"
                                            :disabled="!canApplyRiderDateRange() || riderOrdersLoading"
                                            class="rounded-md bg-neon-400 px-2.5 py-1 text-xs font-bold text-void-950 transition hover:bg-neon-300 disabled:cursor-not-allowed disabled:opacity-60"
                                        >Apply</button>
                                        <button
                                            x-on:click="clearRiderDateRange"
                                            :disabled="riderOrdersLoading"
                                            class="rounded-md border border-void-600 px-2.5 py-1 text-xs font-medium text-void-200 transition hover:border-void-500 hover:text-void-100 disabled:cursor-not-allowed disabled:opacity-60"
                                        >Clear</button>
                                    </div>

                                    <p x-show="riderDateRangeInvalid()" class="text-xs text-red-300">Start date must be before end date.</p>
                                </div>

                                <p x-show="riderOrdersLoading" class="text-sm text-void-400">Loading your orders...</p>

                                <div x-show="!riderOrdersLoading" class="space-y-2">
                                    <template x-for="order in riderOrders" :key="order.uuid">
                                        <div class="rounded-lg border border-void-700 bg-void-900/40 p-3">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-void-100" x-text="shortOrderUuid(order)"></p>
                                                <p
                                                    class="rounded-full border px-2 py-0.5 text-[11px] font-medium"
                                                    :class="riderOrderStatusClass(order)"
                                                    x-text="riderOrderStatus(order)"
                                                ></p>
                                            </div>
                                            <div class="mt-1 flex items-center justify-between">
                                                <p class="text-xs text-void-400" x-text="orderVehicleLabel(order)"></p>
                                                <p class="text-xs text-void-500" x-text="formatOrderDate(order.created_at)"></p>
                                            </div>
                                            <p x-show="order.driver?.name" class="mt-1 text-xs text-void-400" x-text="'Driver: ' + order.driver?.name"></p>
                                            <button
                                                x-on:click="focusOrderOnMap(order)"
                                                class="mt-2 rounded-md border border-void-600 px-2.5 py-1 text-xs font-medium text-void-200 transition hover:border-void-500 hover:text-void-100"
                                            >Preview</button>
                                        </div>
                                    </template>

                                    <p x-show="riderOrders.length === 0" class="text-sm text-void-400" x-text="riderOrdersEmptyMessage()"></p>
                                </div>
                            </div>
                        </div>

                        {{-- ─ IDLE DRIVER ─ --}}
                        <div x-show="step === 'idle' && isDriverMode()" class="space-y-4">
                            <h2 class="font-display text-xl font-bold text-void-50">Driver Mode Active</h2>
                            <p class="text-sm text-void-300">You are set as a driver. Switch back to Rider mode whenever you want to book a ride.</p>
                            <div class="rounded-lg border border-void-700 bg-void-800/40 p-3">
                                <p class="text-sm text-void-200">Vehicle management and payout onboarding have moved to your profile page.</p>
                                <a
                                    href="/profile"
                                    class="mt-2 inline-flex rounded-md border border-void-600 px-2.5 py-1 text-xs font-medium text-void-200 transition hover:border-neon-400/50 hover:text-neon-300"
                                >Open Profile Settings</a>
                            </div>

                            <div class="space-y-2 rounded-lg border border-void-700 bg-void-800/30 p-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-medium uppercase tracking-wide text-void-400">Incoming Orders</p>
                                    <button
                                        x-on:click="fetchIncomingOrders"
                                        class="text-xs font-medium text-neon-400 transition hover:text-neon-300"
                                    >Refresh</button>
                                </div>

                                <p x-show="incomingOrdersLoading" class="text-sm text-void-400">Loading incoming orders...</p>

                                <div x-show="!incomingOrdersLoading" class="space-y-2">
                                    <template x-for="order in incomingOrders" :key="order.uuid">
                                        <div class="rounded-lg border border-void-700 bg-void-900/40 p-3">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-void-100" x-text="order.user?.name || 'Rider'"></p>
                                                <p class="text-xs text-void-400" x-text="shortOrderUuid(order)"></p>
                                            </div>
                                            <p class="mt-1 text-xs text-void-400" x-text="orderVehicleLabel(order)"></p>
                                            <div class="mt-2 flex gap-2">
                                                <button
                                                    x-on:click="focusOrderOnMap(order)"
                                                    class="rounded-md border border-void-600 px-2.5 py-1 text-xs font-medium text-void-200 transition hover:border-void-500 hover:text-void-100"
                                                >Preview</button>
                                                <button
                                                    x-on:click="matchIncomingOrder(order)"
                                                    :disabled="matchingOrderUuid === order.uuid"
                                                    class="rounded-md bg-neon-400 px-2.5 py-1 text-xs font-bold text-void-950 transition hover:bg-neon-300 disabled:cursor-not-allowed disabled:opacity-60"
                                                    x-text="matchingOrderUuid === order.uuid ? 'Matching...' : 'Match'"
                                                ></button>
                                            </div>
                                        </div>
                                    </template>

                                    <p x-show="incomingOrders.length === 0" class="text-sm text-void-400">No incoming orders right now.</p>
                                </div>
                            </div>

                            <div class="space-y-2 rounded-lg border border-void-700 bg-void-800/30 p-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-medium uppercase tracking-wide text-void-400">My Orders</p>
                                    <button
                                        x-on:click="fetchDriverOrders"
                                        class="text-xs font-medium text-neon-400 transition hover:text-neon-300"
                                    >Refresh</button>
                                </div>

                                <p x-show="driverOrdersLoading" class="text-sm text-void-400">Loading your orders...</p>

                                <div x-show="!driverOrdersLoading" class="space-y-2">
                                    <template x-for="order in driverOrders" :key="order.uuid">
                                        <div class="rounded-lg border border-void-700 bg-void-900/40 p-3">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-void-100" x-text="order.user?.name || 'Rider'"></p>
                                                <p
                                                    class="rounded-full border px-2 py-0.5 text-[11px] font-medium"
                                                    :class="driverOrderStatusClass(order)"
                                                    x-text="driverOrderStatus(order)"
                                                ></p>
                                            </div>
                                            <div class="mt-1 flex items-center justify-between">
                                                <p class="text-xs text-void-400" x-text="orderVehicleLabel(order)"></p>
                                                <p class="text-xs text-void-500" x-text="shortOrderUuid(order)"></p>
                                            </div>
                                            <button
                                                x-on:click="focusOrderOnMap(order)"
                                                class="mt-2 rounded-md border border-void-600 px-2.5 py-1 text-xs font-medium text-void-200 transition hover:border-void-500 hover:text-void-100"
                                            >Preview</button>
                                        </div>
                                    </template>

                                    <p x-show="driverOrders.length === 0" class="text-sm text-void-400">No orders assigned to you yet.</p>
                                </div>
                            </div>
                        </div>

                        {{-- ─ SELECT PICKUP ─ --}}
                        <div x-show="step === 'selectPickup'" class="space-y-4">
                            <h2 class="font-display text-xl font-bold text-void-50">Set Pickup</h2>
                            <p class="text-sm text-void-300">Move the map or search below.</p>

                            {{-- Search within pickup step --}}
                            <div>
                                <div class="relative">
                                    <div class="pointer-events-none absolute left-3 top-3">
                                        <svg class="h-4 w-4 text-void-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                    </div>
                                    <input
                                        type="text"
                                        x-model="pickupSearch"
                                        x-on:input="onSearchInput(pickupSearch)"
                                        x-on:focus="if (searchResults.length) showResults = true"
                                        placeholder="Search address..."
                                        class="w-full rounded-lg border border-void-600 bg-void-800 py-2.5 pl-9 pr-4 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                                    >
                                </div>
                                <div x-show="showResults" x-transition class="mt-1 divide-y divide-void-700/50 rounded-lg border border-void-600 bg-void-800">
                                    <template x-for="(result, i) in searchResults" :key="i">
                                        <button
                                            x-on:click="selectSearchResult(result)"
                                            class="flex w-full items-start gap-2.5 px-3 py-2.5 text-left text-sm transition hover:bg-void-700"
                                        >
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-void-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                            <span class="line-clamp-2 text-void-100" x-text="result.display_name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- Live address from map center --}}
                            <div x-show="!showResults" class="rounded-lg border border-neon-400/20 bg-neon-400/5 p-3">
                                <div class="flex items-start gap-2">
                                    <div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-neon-400"></div>
                                    <p class="text-sm text-void-100" x-text="centerAddress || 'Move the map...'"></p>
                                </div>
                            </div>

                            <div x-show="!showResults" class="space-y-2">
                                <button
                                    x-on:click="confirmPickup"
                                    :disabled="!centerAddress || centerAddress === 'Loading...'"
                                    class="w-full rounded-lg bg-neon-400 py-3 text-sm font-bold text-void-950 transition hover:bg-neon-300 disabled:cursor-not-allowed disabled:opacity-40 shadow-[0_0_16px_rgba(200,255,0,0.2)]"
                                >Confirm Pickup</button>

                                <button
                                    x-on:click="cancelBooking"
                                    class="w-full rounded-lg border border-void-700 py-2.5 text-sm font-medium text-void-300 transition hover:border-void-500 hover:text-void-100"
                                >Cancel</button>
                            </div>
                        </div>

                        {{-- ─ SELECT DROPOFF ─ --}}
                        <div x-show="step === 'selectDropoff'" class="space-y-4">
                            <h2 class="font-display text-xl font-bold text-void-50">Set Dropoff</h2>
                            <p class="text-sm text-void-300">Move the map or search below.</p>

                            {{-- Confirmed pickup --}}
                            <div class="rounded-lg border border-neon-400/20 bg-neon-400/5 p-3">
                                <div class="flex items-start gap-2">
                                    <div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-neon-400"></div>
                                    <p class="text-sm text-void-100" x-text="pickup.address"></p>
                                </div>
                            </div>

                            {{-- Search within dropoff step --}}
                            <div>
                                <div class="relative">
                                    <div class="pointer-events-none absolute left-3 top-3">
                                        <svg class="h-4 w-4 text-void-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                                    </div>
                                    <input
                                        type="text"
                                        x-model="dropoffSearch"
                                        x-on:input="onSearchInput(dropoffSearch)"
                                        x-on:focus="if (searchResults.length) showResults = true"
                                        placeholder="Search destination..."
                                        class="w-full rounded-lg border border-void-600 bg-void-800 py-2.5 pl-9 pr-4 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                                    >
                                </div>
                                <div x-show="showResults" x-transition class="mt-1 divide-y divide-void-700/50 rounded-lg border border-void-600 bg-void-800">
                                    <template x-for="(result, i) in searchResults" :key="i">
                                        <button
                                            x-on:click="selectSearchResult(result)"
                                            class="flex w-full items-start gap-2.5 px-3 py-2.5 text-left text-sm transition hover:bg-void-700"
                                        >
                                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-void-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                            <span class="line-clamp-2 text-void-100" x-text="result.display_name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- Live dropoff address from center pin --}}
                            <div x-show="!showResults" class="rounded-lg border border-red-400/20 bg-red-400/5 p-3">
                                <div class="flex items-start gap-2">
                                    <div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-red-400"></div>
                                    <p class="text-sm text-void-100" x-text="centerAddress || 'Move the map...'"></p>
                                </div>
                            </div>

                            <div x-show="!showResults" class="space-y-2">
                                <button
                                    x-on:click="confirmDropoff"
                                    :disabled="!centerAddress || centerAddress === 'Loading...'"
                                    class="w-full rounded-lg bg-neon-400 py-3 text-sm font-bold text-void-950 transition hover:bg-neon-300 disabled:cursor-not-allowed disabled:opacity-40 shadow-[0_0_16px_rgba(200,255,0,0.2)]"
                                >Confirm Dropoff</button>

                                <button
                                    x-on:click="cancelBooking"
                                    class="w-full rounded-lg border border-void-700 py-2.5 text-sm font-medium text-void-300 transition hover:border-void-500 hover:text-void-100"
                                >Cancel</button>
                            </div>
                        </div>

                        {{-- ─ SELECT VEHICLE ─ --}}
                        <div x-show="step === 'selectVehicle'" class="space-y-4">
                            <h2 class="font-display text-xl font-bold text-void-50">Choose Your Ride</h2>

                            <div class="flex items-center gap-3 rounded-lg bg-void-800/60 p-3">
                                <div class="flex flex-col items-center gap-1">
                                    <div class="h-2.5 w-2.5 rounded-full bg-neon-400"></div>
                                    <div class="h-6 w-px border-l border-dashed border-void-500"></div>
                                    <div class="h-2.5 w-2.5 rounded-full bg-red-400"></div>
                                </div>
                                <div class="min-w-0 flex-1 space-y-2">
                                    <p class="truncate text-sm text-void-100" x-text="pickup.address"></p>
                                    <p class="truncate text-sm text-void-100" x-text="dropoff.address"></p>
                                </div>
                            </div>

                            <div x-show="fareLoading" class="py-4 text-center text-sm text-void-400">
                                Calculating fares...
                            </div>

                            <div x-show="!fareLoading" class="space-y-3">
                                <template x-for="fare in fareEstimates" :key="fare.vehicle_type">
                                    <button
                                        x-on:click="selectVehicle(fare.vehicle_type)"
                                        :class="vehicleType === fare.vehicle_type ? 'border-neon-400/50 bg-neon-400/5 shadow-[0_0_12px_rgba(200,255,0,0.1)]' : 'border-void-700 bg-void-800/40 hover:border-void-500'"
                                        class="flex w-full items-center gap-4 rounded-xl border p-4 text-left transition"
                                    >
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-void-700/50 text-2xl">
                                            <span x-text="fare.vehicle_type == 0 ? '\uD83C\uDFCD' : '\uD83D\uDE97'"></span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-display text-sm font-bold text-void-50" x-text="fare.vehicle_type == 0 ? 'Motorbike' : 'Car'"></p>
                                            <p class="text-xs text-void-400" x-text="fare.distance_km + ' km'"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-display text-sm font-bold text-neon-400" x-text="formatCurrency(fare.total_fare, fare.currency_id)"></p>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            <div x-show="vehicleType !== null" class="space-y-2">
                                <button
                                    x-on:click="bookRide"
                                    class="w-full rounded-lg bg-neon-400 py-3 text-sm font-bold text-void-950 transition hover:bg-neon-300 shadow-[0_0_16px_rgba(200,255,0,0.2)]"
                                >Confirm Ride</button>
                            </div>

                            <button
                                x-on:click="cancelBooking"
                                class="w-full rounded-lg border border-void-700 py-2.5 text-sm font-medium text-void-300 transition hover:border-void-500 hover:text-void-100"
                            >Cancel</button>
                        </div>

                        {{-- ─ SEARCHING ─ --}}
                        <div x-show="step === 'searching' || (step === 'active' && !activeOrder?.matched_at)" class="space-y-6 py-4 text-center">
                            <template x-if="!searchTimedOut">
                                <div class="space-y-6">
                                    <div class="mx-auto h-16 w-16 animate-pulse-glow rounded-full bg-neon-400/20 p-4">
                                        <div class="h-full w-full rounded-full bg-neon-400/40"></div>
                                    </div>
                                    <div>
                                        <h2 class="font-display text-xl font-bold text-void-50">Finding your driver...</h2>
                                        <p class="mt-2 text-sm text-void-300">Hang tight, matching you with a nearby driver.</p>
                                        <p class="mt-1 text-xs tabular-nums text-void-500" x-text="Math.floor(searchElapsed / 60) + ':' + String(searchElapsed % 60).padStart(2, '0')"></p>
                                    </div>
                                    <button
                                        x-on:click="cancelRide"
                                        class="w-full rounded-lg border border-red-500/30 py-2.5 text-sm font-medium text-red-400 transition hover:border-red-500/50 hover:bg-red-500/10"
                                    >Cancel Ride</button>
                                </div>
                            </template>

                            <template x-if="searchTimedOut">
                                <div class="space-y-5">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-amber-500/10 text-amber-400">
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    </div>
                                    <div>
                                        <h2 class="font-display text-xl font-bold text-void-50">No drivers nearby</h2>
                                        <p class="mt-2 text-sm text-void-300">We couldn't find a driver in your area right now. You can keep waiting or try again later.</p>
                                    </div>
                                    <div class="space-y-2">
                                        <button
                                            x-on:click="keepWaiting"
                                            class="w-full rounded-lg bg-neon-400/10 py-2.5 text-sm font-medium text-neon-400 transition hover:bg-neon-400/20"
                                        >Keep Waiting</button>
                                        <button
                                            x-on:click="cancelRide"
                                            class="w-full rounded-lg border border-red-500/30 py-2.5 text-sm font-medium text-red-400 transition hover:border-red-500/50 hover:bg-red-500/10"
                                        >Cancel Ride</button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- ─ ACTIVE RIDE (matched with driver) ─ --}}
                        <div x-show="step === 'active' && activeOrder?.matched_at" class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-neon-400/10">
                                    <div class="h-3 w-3 rounded-full bg-neon-400"></div>
                                </div>
                                <div>
                                    <h2 class="font-display text-lg font-bold text-void-50" x-text="getStatusText()"></h2>
                                    <p class="text-xs text-void-400" x-text="'Order: ' + (activeOrder?.uuid?.substring(0, 8) || '') + '...'"></p>
                                </div>
                            </div>

                            <div x-show="activeOrder?.driver" class="rounded-lg border border-void-700 bg-void-800/40 p-4">
                                <p class="text-sm font-medium text-void-100">Driver</p>
                                <p class="mt-1 font-display text-lg font-bold text-void-50" x-text="activeOrder?.driver?.name"></p>
                            </div>

                            <div class="flex items-center gap-3 rounded-lg bg-void-800/40 p-3">
                                <div class="flex flex-col items-center gap-1">
                                    <div class="h-2.5 w-2.5 rounded-full bg-neon-400"></div>
                                    <div class="h-4 w-px border-l border-dashed border-void-500"></div>
                                    <div class="h-2.5 w-2.5 rounded-full bg-red-400"></div>
                                </div>
                                <div class="min-w-0 flex-1 space-y-1.5">
                                    <p class="truncate text-xs text-void-200" x-text="pickup.address"></p>
                                    <p class="truncate text-xs text-void-200" x-text="dropoff.address"></p>
                                </div>
                            </div>
                        </div>

                        {{-- ─ COMPLETED ─ --}}
                        <div x-show="step === 'completed'" class="space-y-6 py-4 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-neon-400/20 text-2xl">
                                <template x-if="activeOrder?.completed_at">
                                    <span>&#10003;</span>
                                </template>
                                <template x-if="!activeOrder?.completed_at">
                                    <span class="text-red-400">&#10005;</span>
                                </template>
                            </div>
                            <div>
                                <h2 class="font-display text-xl font-bold text-void-50" x-text="activeOrder?.completed_at ? 'Ride Complete!' : 'Ride Cancelled'"></h2>
                                <p class="mt-2 text-sm text-void-300" x-text="activeOrder?.completed_at ? 'Thanks for riding with OpenJek.' : 'Your ride has been cancelled.'"></p>
                            </div>
                            <button
                                x-on:click="cancelBooking"
                                class="w-full rounded-lg bg-neon-400 py-3 text-sm font-bold text-void-950 transition hover:bg-neon-300"
                            >Book Another Ride</button>
                        </div>

                    </div>

                    {{-- ── Map container ── --}}
                    <div class="relative flex-1">
                        {{-- Center pin overlay --}}
                        <div
                            x-show="step === 'selectPickup' || step === 'selectDropoff'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-90"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-90"
                            class="pointer-events-none absolute inset-0 z-[1000] flex items-center justify-center"
                        >
                            <div class="relative -mt-10">
                                {{-- Pin head --}}
                                <div
                                    :class="step === 'selectPickup'
                                        ? 'bg-neon-400 shadow-[0_0_24px_rgba(200,255,0,0.7)]'
                                        : 'bg-red-400 shadow-[0_0_24px_rgba(248,113,113,0.7)]'"
                                    class="h-7 w-7 rounded-full border-[3px] border-void-950 transition-all duration-200"
                                    :style="mapMoving ? 'transform: scale(1.25) translateY(-4px)' : 'transform: scale(1) translateY(0)'"
                                ></div>
                                {{-- Pin stem --}}
                                <div
                                    :class="step === 'selectPickup' ? 'bg-neon-400/70' : 'bg-red-400/70'"
                                    class="mx-auto h-6 w-0.5 transition-colors duration-300"
                                ></div>
                                {{-- Ground shadow --}}
                                <div
                                    class="mx-auto h-1.5 w-5 rounded-full bg-black/50 blur-[2px] transition-all duration-200"
                                    :style="mapMoving ? 'transform: scale(0.7); opacity: 0.3' : 'transform: scale(1); opacity: 0.5'"
                                ></div>
                            </div>
                        </div>

                        {{-- Map --}}
                        <div id="map" class="h-full w-full"></div>
                    </div>

                </div>

                {{-- ─ Driver Vehicle Picker Modal ─ --}}
                <div
                    x-show="showVehiclePicker"
                    x-transition.opacity
                    class="fixed inset-0 z-[1200] flex items-center justify-center bg-void-950/80 p-4 backdrop-blur-sm"
                >
                    <div class="w-full max-w-md rounded-2xl border border-void-700 bg-void-900 p-5 shadow-2xl">
                        <h3 class="font-display text-lg font-bold text-void-50">Pick Your Vehicle</h3>
                        <p class="mt-1 text-sm text-void-300">Choose which vehicle you want to drive with now.</p>

                        <div class="mt-4 space-y-2">
                            <template x-for="vehicle in (user?.vehicles || [])" :key="vehicle.id">
                                <button
                                    type="button"
                                    x-on:click="vehiclePickerVehicleId = vehicle.id"
                                    :class="vehiclePickerVehicleId === vehicle.id ? 'border-neon-400/50 bg-neon-400/5' : 'border-void-700 bg-void-800/40 hover:border-void-500'"
                                    class="flex w-full items-center justify-between rounded-lg border px-3 py-2.5 text-left transition"
                                >
                                    <div>
                                        <p class="text-sm font-semibold text-void-100" x-text="vehicle.code"></p>
                                        <p class="text-xs text-void-400" x-text="vehicleTypeLabel(vehicle.vehicle_type)"></p>
                                    </div>
                                    <p
                                        x-show="user?.vehicle_id === vehicle.id"
                                        class="rounded-full border border-neon-400/30 bg-neon-400/10 px-2 py-0.5 text-[11px] font-medium text-neon-400"
                                    >Current</p>
                                </button>
                            </template>
                        </div>

                        <div class="mt-5 flex items-center justify-end gap-2">
                            <button
                                x-on:click="closeVehiclePicker"
                                :disabled="switchModeLoading"
                                class="rounded-lg border border-void-700 px-3 py-2 text-xs font-medium text-void-300 transition hover:border-void-500 hover:text-void-100 disabled:cursor-not-allowed disabled:opacity-60"
                            >Cancel</button>
                            <button
                                x-on:click="confirmVehiclePicker"
                                :disabled="switchModeLoading || !vehiclePickerVehicleId"
                                class="rounded-lg bg-neon-400 px-3 py-2 text-xs font-bold text-void-950 transition hover:bg-neon-300 disabled:cursor-not-allowed disabled:opacity-60"
                                x-text="switchModeLoading ? 'Saving...' : 'Continue as Driver'"
                            ></button>
                        </div>
                    </div>
                </div>

                {{-- ─ Driver Vehicle Onboarding Modal ─ --}}
                <div
                    x-show="showVehicleOnboarding"
                    x-transition.opacity
                    class="fixed inset-0 z-[1200] flex items-center justify-center bg-void-950/80 p-4 backdrop-blur-sm"
                >
                    <div class="w-full max-w-md rounded-2xl border border-void-700 bg-void-900 p-5 shadow-2xl">
                        <h3 class="font-display text-lg font-bold text-void-50">Add your vehicle first</h3>
                        <p class="mt-1 text-sm text-void-300">To switch to driver mode, add at least one vehicle you will use.</p>

                        <div x-show="vehicleErrors.general?.length" class="mt-3 rounded-lg border border-red-500/30 bg-red-500/10 px-3 py-2 text-sm text-red-300" x-text="vehicleErrors.general?.[0]"></div>

                        <div class="mt-4 space-y-3">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-void-300">Vehicle Type</label>
                                <select
                                    x-model.number="vehicleForm.vehicle_type"
                                    class="w-full rounded-lg border border-void-600 bg-void-800 px-3 py-2.5 text-sm text-void-100 focus:border-neon-400/50 focus:outline-none focus:ring-1 focus:ring-neon-400/30"
                                >
                                    <option :value="0">Motorbike</option>
                                    <option :value="1">Car</option>
                                </select>
                                <p x-show="vehicleErrors.vehicle_type?.length" class="mt-1 text-xs text-red-300" x-text="vehicleErrors.vehicle_type?.[0]"></p>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-medium text-void-300">Plate / Vehicle Code</label>
                                <input
                                    type="text"
                                    x-model="vehicleForm.code"
                                    placeholder="B 1234 XYZ"
                                    class="w-full rounded-lg border border-void-600 bg-void-800 px-3 py-2.5 text-sm text-void-100 placeholder-void-500 focus:border-neon-400/50 focus:outline-none focus:ring-1 focus:ring-neon-400/30"
                                >
                                <p x-show="vehicleErrors.code?.length" class="mt-1 text-xs text-red-300" x-text="vehicleErrors.code?.[0]"></p>
                            </div>
                        </div>

                        <div class="mt-5 flex items-center justify-end gap-2">
                            <button
                                x-on:click="closeVehicleOnboarding"
                                :disabled="vehicleLoading || switchModeLoading"
                                class="rounded-lg border border-void-700 px-3 py-2 text-xs font-medium text-void-300 transition hover:border-void-500 hover:text-void-100 disabled:cursor-not-allowed disabled:opacity-60"
                            >Cancel</button>
                            <button
                                x-on:click="submitVehicleOnboarding"
                                :disabled="vehicleLoading || switchModeLoading"
                                class="rounded-lg bg-neon-400 px-3 py-2 text-xs font-bold text-void-950 transition hover:bg-neon-300 disabled:cursor-not-allowed disabled:opacity-60"
                                x-text="vehicleLoading || switchModeLoading ? 'Saving...' : 'Save & Switch'"
                            ></button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

</x-layouts.app>
