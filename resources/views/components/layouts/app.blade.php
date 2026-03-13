@props(['title' => 'OpenJek — Open Source Rides'])

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="OpenJek — The open source ride-sharing platform. API-first. AI-agent ready.">

    <title>{{ $title }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{ $head ?? '' }}
</head>
<body class="bg-void-950 text-void-50 font-body antialiased min-h-screen">
    {{ $slot }}
    @livewireScripts
</body>
</html>
