<x-layouts.app title="Profile">

<div class="p-10 max-w-2xl mx-auto">

    <h1 class="text-3xl font-bold mb-8">
        Your Profile 👤
    </h1>

    <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
        <div class="mb-6">
            <span class="block text-sm font-medium text-gray-400 mb-1">Name</span>
            <div class="text-xl text-white font-semibold">{{ Auth::user()->name }}</div>
        </div>
        
        <div class="mb-6">
            <span class="block text-sm font-medium text-gray-400 mb-1">Email Address</span>
            <div class="text-lg text-white">{{ Auth::user()->email }}</div>
        </div>

        <div class="mb-8">
            <span class="block text-sm font-medium text-gray-400 mb-1">Phone Number</span>
            <div class="text-lg text-white">{{ Auth::user()->phone ?? 'Not provided' }}</div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-md transition duration-300 ease-in-out transform hover:scale-[1.02]">
                Logout
            </button>
        </form>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition">
            ← Back to Dashboard
        </a>
    </div>

</div>

</x-layouts.app>