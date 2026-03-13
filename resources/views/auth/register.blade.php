<x-layouts.app title="Register — OpenJek">

    <div class="flex min-h-screen items-center justify-center px-6 py-12">
        <div class="pointer-events-none fixed inset-0" style="background: radial-gradient(ellipse 50% 40% at 50% 40%, rgba(200,255,0,0.04) 0%, transparent 100%);"></div>

        <div class="animate-fade-up relative w-full max-w-md">
            {{-- Logo --}}
            <div class="mb-10 text-center">
                <a href="/" class="inline-flex items-center gap-1 font-display text-3xl font-bold tracking-tight">
                    <span class="text-void-100">Open</span><span class="text-neon-400">Jek</span>
                </a>
                <p class="mt-2 text-sm text-void-300">Create your account</p>
            </div>

            {{-- Form --}}
            <form
                x-data="registerForm"
                x-on:submit.prevent="submit"
                class="rounded-2xl border border-void-700/50 bg-void-900/60 p-8 backdrop-blur-sm"
            >
                <template x-if="errors.general">
                    <div class="mb-6 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-400">
                        <template x-for="msg in errors.general"><p x-text="msg"></p></template>
                    </div>
                </template>

                {{-- Name --}}
                <div class="mb-4">
                    <label for="reg-name" class="mb-1.5 block text-sm font-medium text-void-200">Name</label>
                    <input
                        id="reg-name"
                        type="text"
                        x-model="name"
                        placeholder="Your name"
                        required
                        class="w-full rounded-lg border border-void-600 bg-void-800 px-4 py-3 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                    >
                    <template x-if="errors.name">
                        <p class="mt-1 text-xs text-red-400" x-text="errors.name[0]"></p>
                    </template>
                </div>

                {{-- Phone --}}
                <div class="mb-4">
                    <label for="reg-phone" class="mb-1.5 block text-sm font-medium text-void-200">Phone</label>
                    <div class="flex gap-2">
                        <div
                            class="relative w-44"
                            x-on:keydown.escape.prevent.stop="closeCountryPicker()"
                            x-on:click.outside="closeCountryPicker()"
                        >
                            <button
                                id="reg-country-code"
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
                                x-cloak
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
                            id="reg-phone"
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

                {{-- Email (optional) --}}
                <div class="mb-4">
                    <label for="reg-email" class="mb-1.5 block text-sm font-medium text-void-200">
                        Email <span class="text-void-400">(optional)</span>
                    </label>
                    <input
                        id="reg-email"
                        type="email"
                        x-model="email"
                        placeholder="you@example.com"
                        class="w-full rounded-lg border border-void-600 bg-void-800 px-4 py-3 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                    >
                    <template x-if="errors.email">
                        <p class="mt-1 text-xs text-red-400" x-text="errors.email[0]"></p>
                    </template>
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="reg-password" class="mb-1.5 block text-sm font-medium text-void-200">Password</label>
                    <input
                        id="reg-password"
                        type="password"
                        x-model="password"
                        placeholder="Create a password"
                        required
                        class="w-full rounded-lg border border-void-600 bg-void-800 px-4 py-3 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                    >
                    <template x-if="errors.password">
                        <p class="mt-1 text-xs text-red-400" x-text="errors.password[0]"></p>
                    </template>
                </div>

                {{-- Confirm Password --}}
                <div class="mb-6">
                    <label for="reg-password-confirm" class="mb-1.5 block text-sm font-medium text-void-200">Confirm Password</label>
                    <input
                        id="reg-password-confirm"
                        type="password"
                        x-model="password_confirmation"
                        placeholder="Confirm your password"
                        required
                        class="w-full rounded-lg border border-void-600 bg-void-800 px-4 py-3 text-sm text-void-50 placeholder-void-400 transition focus:border-neon-400/50 focus:ring-1 focus:ring-neon-400/30"
                    >
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full rounded-lg bg-neon-400 py-3 text-sm font-bold text-void-950 transition hover:bg-neon-300 disabled:cursor-not-allowed disabled:opacity-50 shadow-[0_0_16px_rgba(200,255,0,0.2)]"
                >
                    <span x-show="!loading">Create Account</span>
                    <span x-show="loading" class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Creating account...
                    </span>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-void-400">
                Already have an account?
                <a href="/login" class="font-medium text-neon-400 transition hover:text-neon-300">Sign in</a>
            </p>
        </div>
    </div>

</x-layouts.app>
