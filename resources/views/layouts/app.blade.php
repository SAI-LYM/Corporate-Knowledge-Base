<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MON Knowledge Hub') — MON Logistics</title>

    {{-- Prompt font: renders Thai + Latin cleanly (CLAUDE.md §1, §4). Pinned weights. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-mon-gradient min-h-screen font-sans text-gray-800 antialiased">
    <div class="mx-auto max-w-6xl px-4 py-6 md:py-8">

        {{-- Header card --}}
        <header class="rounded-mon-card bg-white shadow-mon-card px-6 py-4 md:px-8 md:py-5">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/mon-logo.png') }}" alt="MON Logistics" class="h-9 w-auto md:h-10">
                    <span class="hidden border-l border-gray-200 pl-3 sm:block">
                        <span class="block text-base font-semibold leading-tight text-mon-primary">Knowledge Hub</span>
                        <span class="block text-xs text-gray-500">Internal Developer Portal · ศูนย์ความรู้ภายใน</span>
                    </span>
                </a>

                {{-- Slot for nav / user menu in later steps --}}
                @hasSection('header-actions')
                    <div class="flex items-center gap-3">@yield('header-actions')</div>
                @endif
            </div>
        </header>

        {{-- Page content --}}
        <main class="mt-6">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="mt-8 pb-2 text-center text-xs text-white/80">
            © {{ date('Y') }} MON Logistics Group — Internal use only · ใช้ภายในองค์กรเท่านั้น
        </footer>
    </div>
</body>
</html>
