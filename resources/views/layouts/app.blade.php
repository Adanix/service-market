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
    {{-- Accessible Skip Link --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-[var(--color-teal-500)] focus:text-white focus:rounded-xl shadow-lg">
        Skip to content
    </a>

    {{-- Persistent Demo Banner --}}
    @include('partials.demo-banner')

    {{-- Header --}}
    @include('partials.header')

    {{-- Main Page Content --}}
    <main id="main-content" class="flex-1 py-8 md:py-12">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            @include('partials.flash')
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    @include('partials.footer')
</body>
</html>
