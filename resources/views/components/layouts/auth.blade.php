<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') | {{ config('marketplace.name', 'ServiceLocal') }}@else{{ config('marketplace.name', 'ServiceLocal') }}@endif</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col antialiased bg-[var(--color-page)] text-[var(--color-ink)]">
    @include('partials.demo-banner')

    <header class="py-6 px-4 sm:px-6 lg:px-8 border-b border-[var(--color-line)] bg-white text-center">
        <a href="{{ route('home') }}" class="text-2xl font-bold tracking-tight text-[var(--color-navy-900)] inline-flex items-center gap-2">
            ServiceLocal
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-[var(--color-teal-500)]/15 text-[var(--color-teal-600)] border border-[var(--color-teal-500)]/30">
                Demo
            </span>
        </a>
    </header>

    <main id="main-content" class="flex-1 py-10 md:py-16">
        <div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8">
            @include('partials.flash')
            @yield('content')
        </div>
    </main>

    <footer class="py-6 text-center text-xs text-[var(--color-muted)] border-t border-[var(--color-line)] bg-white">
        Demo data only. Payments are simulated and no real money moves.
    </footer>
</body>
</html>
