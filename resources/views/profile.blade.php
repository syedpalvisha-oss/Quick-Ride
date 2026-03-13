<x-layouts.app title="Edit Profile — OpenJek">
    <div class="min-h-screen px-6 py-12">
        <div class="pointer-events-none fixed inset-0" style="background: radial-gradient(ellipse 55% 45% at 50% 35%, rgba(200,255,0,0.05) 0%, transparent 100%);"></div>

        <div class="relative mx-auto w-full max-w-2xl">
            <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <a href="/" class="inline-flex items-center gap-1 font-display text-3xl font-bold tracking-tight">
                    <span class="text-void-100">Open</span><span class="text-neon-400">Jek</span>
                </a>
                <a
                    href="/home"
                    class="inline-flex items-center justify-center rounded-lg border border-void-700 px-4 py-2 text-sm font-medium text-void-200 transition hover:border-neon-400/50 hover:text-neon-300"
                >Back to Home</a>
            </div>

            <form
                x-data="profileForm"
                x-on:submit.prevent="submit"
                class="animate-fade-up rounded-2xl border border-void-700/50 bg-void-900/70 p-8 backdrop-blur-sm"
            >
                <div class="mb-6">
                    <h1 class="font-display text-2xl font-bold text-void-50">Edit Profile</h1>
                    <p class="mt-1 text-sm text-void-300">Update your account details.</p>
                </div>

                <template x-if="errors.general">
                    <div class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                        <template x-for="msg in errors.general" :key="msg">
                            <p x-text="msg"></p>
                        </template>
                    </div>
                </template>

                <template x-if="successMessage">
                    <div class="mb-6 rounded-lg border border-neon-400/30 bg-neon-400/10 px-4 py-3 text-sm text-neon-200">
                        <p x-text="successMessage"></p>
                    </div>
                </template>

                <div x-show="loading" class="space-y-3">
                    <div class="h-10 animate-pulse rounded-lg bg-void-800"></div>
                    <div class="h-10 animate-pulse rounded-lg bg-void-800"></div>
                    <div class="h-10 animate-pulse rounded-lg bg-void-800"></div>
                </div>

                <div x-show="!loading" class="space-y-5">
                    <div>
                        <label for="profile-name" class="mb-1.5 block text-sm font-medium text-void-200">Name</label>
                        <input
                            id="profile-name"
                            type="text"
                            x-model="name"
                            placeholder="Your full name"
                            required
                            class="w-full rounded-lg border border-void-600 bg-void-800 px-4 py-3 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                        >
                        <template x-if="errors.name">
                            <p class="mt-1 text-xs text-red-400" x-text="errors.name[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label for="profile-phone" class="mb-1.5 block text-sm font-medium text-void-200">Phone</label>
                        <div class="flex gap-2">
                            <div
                                class="relative w-44"
                                x-on:keydown.escape.prevent.stop="closeCountryPicker()"
                                x-on:click.outside="closeCountryPicker()"
                            >
                                <button
                                    id="profile-country-code"
                                    type="button"
                                    x-on:click="countryPickerOpen ? closeCountryPicker() : openCountryPicker()"
                                    class="flex w-full items-center justify-between rounded-lg border border-void-600 bg-void-800 px-3 py-3 text-sm text-void-50 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                                >
                                    <span x-text="selectedCountryLabel()"></span>
                                    <svg class="h-4 w-4 text-void-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 011.06 0L10 10.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 8.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div
                                    x-show="countryPickerOpen"
                                    class="absolute left-0 top-full z-30 mt-2 w-72 rounded-lg border border-void-600 bg-void-900 p-2 shadow-lg"
                                >
                                    <input
                                        type="text"
                                        x-ref="countrySearchInput"
                                        x-model="countrySearch"
                                        placeholder="Search country, ISO, or +code"
                                        class="w-full rounded-md border border-void-600 bg-void-800 px-3 py-2 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                                    >

                                    <div class="mt-2 max-h-56 overflow-y-auto rounded-md border border-void-700">
                                        <template x-for="country in filteredCountryOptions()" :key="country.iso2">
                                            <button
                                                type="button"
                                                x-on:click="selectCountry(country)"
                                                class="flex w-full items-center justify-between px-3 py-2 text-left text-sm text-void-200 transition hover:bg-void-700/60"
                                            >
                                                <span class="font-medium" x-text="country.name"></span>
                                                <span class="text-void-400" x-text="`${country.iso2} • ${country.dialCode}`"></span>
                                            </button>
                                        </template>

                                        <p
                                            x-show="filteredCountryOptions().length === 0"
                                            class="px-3 py-2 text-xs text-void-400"
                                        >
                                            No countries found.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <input
                                id="profile-phone"
                                type="tel"
                                x-model="phone"
                                placeholder="81234567890"
                                required
                                class="w-full rounded-lg border border-void-600 bg-void-800 px-4 py-3 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                            >
                        </div>
                        <template x-if="errors.phone">
                            <p class="mt-1 text-xs text-red-400" x-text="errors.phone[0]"></p>
                        </template>
                    </div>

                    <div>
                        <label for="profile-email" class="mb-1.5 block text-sm font-medium text-void-200">
                            Email <span class="text-void-400">(optional)</span>
                        </label>
                        <input
                            id="profile-email"
                            type="email"
                            x-model="email"
                            placeholder="you@example.com"
                            class="w-full rounded-lg border border-void-600 bg-void-800 px-4 py-3 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                        >
                        <template x-if="errors.email">
                            <p class="mt-1 text-xs text-red-400" x-text="errors.email[0]"></p>
                        </template>
                    </div>

                    <div class="rounded-xl border border-void-700 bg-void-800/30 p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="font-display text-lg font-semibold text-void-50">Vehicles & Payout</h2>
                                <p class="text-sm text-void-300">Manage your vehicles and Stripe payout onboarding.</p>
                            </div>
                            <button
                                type="button"
                                x-on:click="openVehicleOnboarding"
                                class="rounded-lg border border-void-700 px-3 py-2 text-sm font-medium text-void-200 transition hover:border-neon-400/50 hover:text-neon-300"
                            >Add Vehicle</button>
                        </div>

                        <div class="mt-4 rounded-lg border border-void-700 bg-void-900/40 p-3">
                            <p class="text-xs font-medium uppercase tracking-wide text-void-400">Stripe Connect</p>
                            <p
                                class="mt-1 text-sm"
                                :class="user?.driver_profile?.charges_enabled && user?.driver_profile?.payouts_enabled ? 'text-neon-400' : 'text-amber-300'"
                                x-text="user?.driver_profile?.charges_enabled && user?.driver_profile?.payouts_enabled ? 'Onboarding complete. You can receive payouts.' : 'Onboarding required to receive payouts.'"
                            ></p>
                        </div>

                        <button
                            type="button"
                            x-show="(user?.vehicles_count ?? 0) > 0 && !(user?.driver_profile?.charges_enabled && user?.driver_profile?.payouts_enabled)"
                            x-on:click="startStripeOnboarding"
                            :disabled="stripeOnboardingLoading"
                            class="mt-3 w-full rounded-lg bg-neon-400 py-2.5 text-sm font-bold text-void-950 transition hover:bg-neon-300 disabled:cursor-not-allowed disabled:opacity-60"
                            x-text="stripeOnboardingLoading ? 'Redirecting...' : 'Complete Stripe Onboarding'"
                        ></button>

                        <div class="mt-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-medium uppercase tracking-wide text-void-400">Your Vehicles</p>
                                <p class="text-xs text-void-500" x-text="`${user?.vehicles_count ?? 0} linked`"></p>
                            </div>

                            <div class="space-y-2">
                                <template x-for="vehicle in (user?.vehicles || [])" :key="vehicle.id">
                                    <div class="rounded-lg border border-void-700 bg-void-900/40 px-3 py-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-sm font-semibold text-void-100" x-text="vehicle.code"></p>
                                            <div class="flex items-center gap-2">
                                                <p
                                                    x-show="user?.vehicle_id === vehicle.id"
                                                    class="rounded-full border border-neon-400/30 bg-neon-400/10 px-2 py-0.5 text-[11px] font-medium text-neon-400"
                                                >Active</p>
                                                <p
                                                    class="rounded-full border border-void-600 bg-void-700/50 px-2 py-0.5 text-[11px] text-void-300"
                                                    x-text="vehicleTypeLabel(vehicle.vehicle_type)"
                                                ></p>
                                            </div>
                                        </div>
                                        <div class="mt-2 flex flex-wrap items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                x-show="user?.vehicle_id !== vehicle.id"
                                                x-on:click="setActiveVehicle(vehicle.id)"
                                                :disabled="switchModeLoading"
                                                class="rounded-md border border-void-600 px-2.5 py-1 text-[11px] font-medium text-void-200 transition hover:border-neon-400/50 hover:text-neon-300 disabled:cursor-not-allowed disabled:opacity-60"
                                            >Set Active</button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <p x-show="(user?.vehicles || []).length === 0" class="text-sm text-void-400">No vehicles added yet. Add your first vehicle to enable driver payouts.</p>
                        </div>

                        <button
                            type="button"
                            x-show="isDriverMode()"
                            x-on:click="switchToRider"
                            :disabled="switchModeLoading"
                            class="mt-3 w-full rounded-lg border border-void-700 py-2.5 text-sm font-medium text-void-200 transition hover:border-void-500 hover:text-void-100 disabled:cursor-not-allowed disabled:opacity-60"
                            x-text="switchModeLoading ? 'Saving...' : 'Switch to Rider Mode'"
                        ></button>
                    </div>
                </div>

                <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <button
                        type="button"
                        x-on:click="logout"
                        class="rounded-lg border border-void-700 px-4 py-2 text-sm font-medium text-void-300 transition hover:border-void-500 hover:text-void-100"
                    >Logout</button>

                    <button
                        type="submit"
                        :disabled="loading || saving"
                        class="rounded-lg bg-neon-400 px-5 py-2.5 text-sm font-bold text-void-950 transition hover:bg-neon-300 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span x-show="!saving">Save Changes</span>
                        <span x-show="saving">Saving...</span>
                    </button>
                </div>

                <div
                    x-show="showVehicleOnboarding"
                    x-transition.opacity
                    class="fixed inset-0 z-[1200] flex items-center justify-center bg-void-950/80 p-4 backdrop-blur-sm"
                >
                    <div class="w-full max-w-md rounded-2xl border border-void-700 bg-void-900 p-5 shadow-2xl">
                        <h3 class="font-display text-lg font-bold text-void-50">Add Vehicle</h3>
                        <p class="mt-1 text-sm text-void-300">Add another vehicle you want to drive with.</p>

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
                                type="button"
                                x-on:click="closeVehicleOnboarding"
                                :disabled="vehicleLoading"
                                class="rounded-lg border border-void-700 px-3 py-2 text-xs font-medium text-void-300 transition hover:border-void-500 hover:text-void-100 disabled:cursor-not-allowed disabled:opacity-60"
                            >Cancel</button>
                            <button
                                type="button"
                                x-on:click="submitVehicleOnboarding"
                                :disabled="vehicleLoading"
                                class="rounded-lg bg-neon-400 px-3 py-2 text-xs font-bold text-void-950 transition hover:bg-neon-300 disabled:cursor-not-allowed disabled:opacity-60"
                                x-text="vehicleLoading ? 'Saving...' : 'Save Vehicle'"
                            ></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
