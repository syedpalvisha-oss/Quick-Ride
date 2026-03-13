<x-layouts.app title="OpenJek — Open Source Rides">

    {{-- ── Navigation ─────────────────────────────────────── --}}
    <nav
        x-data="{ scrolled: false }"
        x-on:scroll.window="scrolled = window.scrollY > 40"
        :class="scrolled ? 'bg-void-950/80 backdrop-blur-xl border-b border-void-800/60' : 'bg-transparent'"
        class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
    >
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="/" class="flex items-center gap-1 font-display text-xl font-bold tracking-tight">
                <span class="text-void-100">Open</span><span class="text-neon-400">Jek</span>
            </a>

            <div class="flex items-center gap-3">
                <a href="/login" class="rounded-lg px-4 py-2 text-sm font-medium text-void-200 transition hover:text-neon-400">
                    Login
                </a>
                <a href="/register" class="rounded-lg bg-neon-400 px-5 py-2 text-sm font-semibold text-void-950 transition hover:bg-neon-300 shadow-[0_0_20px_rgba(200,255,0,0.2)] hover:shadow-[0_0_30px_rgba(200,255,0,0.4)]">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    {{-- ── Hero ───────────────────────────────────────────── --}}
    <section class="relative flex min-h-screen items-center justify-center overflow-hidden">
        {{-- Grid pattern --}}
        <div class="absolute inset-0" style="background-image:
            linear-gradient(rgba(200,255,0,0.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(200,255,0,0.035) 1px, transparent 1px);
            background-size: 80px 80px;"></div>

        {{-- Central glow --}}
        <div class="absolute inset-0" style="background: radial-gradient(ellipse 60% 50% at 50% 45%, rgba(200,255,0,0.07) 0%, transparent 100%);"></div>

        {{-- City-map accent lines --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute inset-x-0 top-[35%] h-px bg-gradient-to-r from-transparent via-neon-400/10 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-[28%] h-px bg-gradient-to-r from-transparent via-neon-400/[0.04] to-transparent"></div>
            <div class="absolute inset-y-0 left-[20%] w-px bg-gradient-to-b from-transparent via-neon-400/[0.06] to-transparent"></div>
            <div class="absolute inset-y-0 right-[22%] w-px bg-gradient-to-b from-transparent via-neon-400/[0.04] to-transparent"></div>
            <div class="absolute left-[20%] top-[35%] h-2 w-2 -translate-x-1/2 -translate-y-1/2 rounded-full bg-neon-400/20"></div>
            <div class="absolute right-[22%] top-[35%] h-1.5 w-1.5 -translate-x-1/2 -translate-y-1/2 rounded-full bg-neon-400/10"></div>
        </div>

        {{-- Bottom fade --}}
        <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-void-950 to-transparent"></div>

        {{-- Content --}}
        <div class="relative z-10 px-6 text-center">
            <h1 class="font-display font-extrabold leading-[0.85] tracking-tighter" style="font-size: clamp(4rem, 14vw, 12rem);">
                <span class="animate-fade-up block text-transparent" style="-webkit-text-stroke: 2px rgba(200,255,0,0.35); animation-delay: 0ms;">OPEN</span>
                <span class="animate-fade-up block text-neon-400" style="animation-delay: 120ms;">JEK</span>
            </h1>

            <p class="animate-fade-up mx-auto mt-6 max-w-lg text-lg text-void-200 md:text-xl" style="animation-delay: 250ms;">
                The open source ride-sharing platform.<br class="hidden sm:block">
                API-first. AI-agent ready. Community driven.
            </p>

            <div class="animate-fade-up mt-10 flex flex-wrap justify-center gap-4" style="animation-delay: 400ms;">
                <a href="/register" class="animate-pulse-glow rounded-xl bg-neon-400 px-8 py-3.5 text-sm font-bold uppercase tracking-wider text-void-950 transition hover:bg-neon-300">
                    Start Riding
                </a>
                <a href="/docs/api" class="group flex items-center gap-2 rounded-xl border border-void-600 px-8 py-3.5 text-sm font-medium text-void-100 transition hover:border-neon-400/40 hover:text-neon-400">
                    API Documentation
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ── Feature Strip ──────────────────────────────────── --}}
    <section class="relative border-y border-void-800/50 bg-void-900/40">
        <div class="animate-shimmer absolute inset-0"></div>
        <div class="relative mx-auto flex max-w-5xl flex-wrap items-center justify-center gap-x-12 gap-y-4 px-6 py-6 text-sm font-medium text-void-300">
            <span class="flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full bg-neon-400"></span>
                Open Source
            </span>
            <span class="flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full bg-neon-400"></span>
                API First
            </span>
            <span class="flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full bg-neon-400"></span>
                AI Agent Ready
            </span>
            <span class="flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full bg-neon-400"></span>
                Multi-Currency
            </span>
            <span class="flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full bg-neon-400"></span>
                PostGIS Spatial
            </span>
        </div>
    </section>

    {{-- ── Features ───────────────────────────────────────── --}}
    <section class="relative py-28">
        <div class="mx-auto max-w-6xl px-6">
            <div class="mb-16 text-center">
                <h2 class="font-display text-3xl font-bold tracking-tight text-void-50 sm:text-4xl">Built Different</h2>
                <p class="mt-3 text-void-300">Not another black-box platform. This is yours.</p>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                {{-- Open Source --}}
                <div class="group relative overflow-hidden rounded-2xl border border-void-700/50 bg-void-900/50 p-8 backdrop-blur-sm transition-all duration-300 hover:border-neon-400/30 hover:bg-void-800/50">
                    <div class="absolute inset-0 bg-gradient-to-br from-neon-400/[0.03] to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-neon-400/10 text-neon-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/></svg>
                        </div>
                        <h3 class="font-display text-xl font-bold text-void-50">Open Source</h3>
                        <p class="mt-3 text-sm leading-relaxed text-void-300">
                            Fork it. Modify it. Make it yours. Full transparency — no vendor lock-in, no hidden fees. Built by the community, for the community.
                        </p>
                    </div>
                </div>

                {{-- API First --}}
                <div class="group relative overflow-hidden rounded-2xl border border-void-700/50 bg-void-900/50 p-8 backdrop-blur-sm transition-all duration-300 hover:border-neon-400/30 hover:bg-void-800/50">
                    <div class="absolute inset-0 bg-gradient-to-br from-neon-400/[0.03] to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-neon-400/10 text-neon-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                        </div>
                        <h3 class="font-display text-xl font-bold text-void-50">API First</h3>
                        <p class="mt-3 text-sm leading-relaxed text-void-300">
                            RESTful API with Sanctum authentication. Build any client — mobile, web, CLI, bot. Versioned endpoints with full documentation.
                        </p>
                    </div>
                </div>

                {{-- AI Agent Ready --}}
                <div class="group relative overflow-hidden rounded-2xl border border-void-700/50 bg-void-900/50 p-8 backdrop-blur-sm transition-all duration-300 hover:border-neon-400/30 hover:bg-void-800/50">
                    <div class="absolute inset-0 bg-gradient-to-br from-neon-400/[0.03] to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>
                    <div class="relative">
                        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-neon-400/10 text-neon-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z"/></svg>
                        </div>
                        <h3 class="font-display text-xl font-bold text-void-50">AI Agent Ready</h3>
                        <p class="mt-3 text-sm leading-relaxed text-void-300">
                            Designed for LLMs and AI agents to interact. Book rides programmatically, integrate with chatbots, or build autonomous transportation agents.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── How It Works ───────────────────────────────────── --}}
    <section class="relative overflow-hidden border-t border-void-800/50 py-28">
        <div class="mx-auto max-w-5xl px-6">
            <div class="mb-16 text-center">
                <h2 class="font-display text-3xl font-bold tracking-tight text-void-50 sm:text-4xl">How It Works</h2>
                <p class="mt-3 text-void-300">Three steps. That's it.</p>
            </div>

            <div class="relative grid gap-12 md:grid-cols-3 md:gap-8">
                {{-- Connector line --}}
                <div class="pointer-events-none absolute left-[16.66%] right-[16.66%] top-10 hidden h-px md:block">
                    <div class="h-full w-full border-t-2 border-dashed border-void-600"></div>
                </div>

                <div class="relative text-center">
                    <div class="relative z-10 mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl border border-void-600 bg-void-900 font-display text-2xl font-bold text-neon-400">
                        01
                    </div>
                    <h3 class="font-display text-lg font-bold text-void-50">Set Your Pickup</h3>
                    <p class="mt-2 text-sm text-void-300">Tap the map or search for an address. We'll find you.</p>
                </div>

                <div class="relative text-center">
                    <div class="relative z-10 mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl border border-void-600 bg-void-900 font-display text-2xl font-bold text-neon-400">
                        02
                    </div>
                    <h3 class="font-display text-lg font-bold text-void-50">Get Matched</h3>
                    <p class="mt-2 text-sm text-void-300">Choose your ride type. A nearby driver matches you instantly.</p>
                </div>

                <div class="relative text-center">
                    <div class="relative z-10 mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-2xl border border-void-600 bg-void-900 font-display text-2xl font-bold text-neon-400">
                        03
                    </div>
                    <h3 class="font-display text-lg font-bold text-void-50">Enjoy the Ride</h3>
                    <p class="mt-2 text-sm text-void-300">Track your trip in real-time. Pay, rate, and you're done.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── CTA ────────────────────────────────────────────── --}}
    <section class="py-28">
        <div class="mx-auto max-w-3xl px-6">
            <div class="relative overflow-hidden rounded-3xl border border-void-700/50 bg-void-900/60 p-12 text-center backdrop-blur-sm md:p-16">
                <div class="absolute inset-0" style="background: radial-gradient(ellipse at 50% 100%, rgba(200,255,0,0.04) 0%, transparent 60%);"></div>
                <div class="relative">
                    <h2 class="font-display text-3xl font-bold tracking-tight text-void-50 sm:text-4xl">
                        Ready to ride<br>the open way?
                    </h2>
                    <p class="mx-auto mt-4 max-w-md text-void-300">
                        Join the community building the future of open transportation.
                    </p>
                    <a href="/register" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-neon-400 px-8 py-3.5 text-sm font-bold uppercase tracking-wider text-void-950 shadow-[0_0_24px_rgba(200,255,0,0.25)] transition hover:bg-neon-300 hover:shadow-[0_0_36px_rgba(200,255,0,0.4)]">
                        Create Your Account
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Footer ─────────────────────────────────────────── --}}
    <footer class="border-t border-void-800/50 py-10">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 sm:flex-row">
            <a href="/" class="flex items-center gap-1 font-display text-sm font-bold tracking-tight">
                <span class="text-void-300">Open</span><span class="text-neon-400">Jek</span>
            </a>
            <div class="flex items-center gap-6 text-sm text-void-400">
                <a href="/docs/api" class="transition hover:text-void-200">API Docs</a>
                <a href="/admin" class="transition hover:text-void-200">Admin</a>
            </div>
            <p class="text-xs text-void-500">&copy; {{ date('Y') }} OpenJek. Open source under MIT.</p>
        </div>
    </footer>

</x-layouts.app>
