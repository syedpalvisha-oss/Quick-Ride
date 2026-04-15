<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'QuickRide' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-black text-white">

    <!-- NAVBAR -->
    <nav class="flex justify-between items-center px-8 py-4 bg-gray-900 border-b border-gray-800">

        <!-- Logo -->
        <h1 class="text-xl font-bold">
            <span class="text-white">Quick</span>
            <span class="text-green-400">Ride</span>
        </h1>

        <!-- Links -->
        @auth
        <div class="flex items-center gap-6 text-sm">

            @if(Auth::user()->role === 'driver')
                <a href="/driver/dashboard" class="hover:text-indigo-400">Dashboard</a>
                <a href="/driver/profile" class="hover:text-indigo-400">Profile</a>
            @else
                <a href="/home" class="hover:text-green-400">Home</a>
                <a href="/book-ride" class="hover:text-green-400">Book Ride</a>
                <a href="/history" class="hover:text-green-400">History</a>
                <a href="/profile" class="hover:text-green-400">Profile</a>
            @endif

            <form method="POST" action="/logout">
                @csrf
                <button class="bg-red-500 px-3 py-1 rounded text-white">
                    Logout
                </button>
            </form>

        </div>
        @endauth

    </nav>

    <!-- CONTENT -->
    <main>
        {{ $slot }}
    </main>

    @if(session('success'))
        <div id="toast-success" class="fixed top-5 right-5 px-6 py-4 rounded-lg shadow-2xl bg-green-500 text-white font-bold transition-opacity duration-500 z-[100]">
            {{ session('success') }} ✅
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-success');
                if(toast) {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3000);
        </script>
    @endif

    @if(session('error'))
        <div id="toast-error" class="fixed top-5 right-5 px-6 py-4 rounded-lg shadow-2xl bg-red-500 text-white font-bold transition-opacity duration-500 z-[100]">
            {{ session('error') }}
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-error');
                if(toast) {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3000);
        </script>
    @endif

</body>
</html>