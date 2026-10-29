<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Receipt') | {{ config('marketplace.name', 'ServiceLocal') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-border { border: 1px solid #ddd !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-[var(--color-ink)] py-8 antialiased">
    <div class="mx-auto w-full max-w-2xl px-4 sm:px-6">
        @yield('content')
    </div>
</body>
</html>
