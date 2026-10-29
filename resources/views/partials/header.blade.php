<header class="bg-white border-b border-[var(--color-line)] sticky top-0 z-40" x-data="{ mobileMenuOpen: false }">
    <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            
            {{-- Left: Wordmark & Demo Badge --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight text-[var(--color-navy-900)] flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-[var(--color-teal-600)] rounded-lg">
                    ServiceLocal
                </a>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-[var(--color-teal-500)]/15 text-[var(--color-teal-600)] border border-[var(--color-teal-500)]/30"
                      title="Simulated marketplace. No real payments.">
                    Demo
                </span>
            </div>

            {{-- Center: Desktop Primary Navigation --}}
            <nav class="hidden md:flex items-center gap-6" aria-label="Main Navigation">
                <a href="{{ route('services.index') }}" class="text-sm font-medium text-[var(--color-ink)] hover:text-[var(--color-teal-600)] transition">
                    Browse services
                </a>
                <a href="{{ route('docs') }}" class="text-sm font-medium text-[var(--color-ink)] hover:text-[var(--color-teal-600)] transition">
                    Docs
                </a>
                <a href="{{ route('help') }}" class="text-sm font-medium text-[var(--color-ink)] hover:text-[var(--color-teal-600)] transition">
                    Help
                </a>
            </nav>

            {{-- Right: Auth / Role Controls --}}
            <div class="hidden md:flex items-center gap-4">
                @guest
                    <x-ui.button variant="secondary" size="sm" :href="route('demo.sign-in')">
                        Log in
                    </x-ui.button>
                @endguest

                @auth
                    @php
                        $user = auth()->user();
                        $unreadNotificationsCount = $user->unreadNotifications()->count();
                        $dashboardRoute = match($user->role->value) {
                            'admin' => route('admin.dashboard'),
                            'provider' => route('provider.dashboard'),
                            default => route('customer.dashboard'),
                        };
                    @endphp

                    {{-- Role Dashboard Link --}}
                    <a href="{{ $dashboardRoute }}" class="text-sm font-medium text-[var(--color-teal-600)] hover:underline">
                        {{ ucfirst($user->role->value) }} workspace
                    </a>

                    {{-- Notification Bell --}}
                    <a href="{{ route('notifications.index') }}"
                       class="relative p-2 rounded-xl text-[var(--color-muted)] hover:text-[var(--color-ink)] hover:bg-[var(--color-page)] focus:outline-none focus:ring-2 focus:ring-[var(--color-teal-600)] transition"
                       aria-label="Notifications ({{ $unreadNotificationsCount }} unread)">
                        <x-ui.icon name="bell" class="h-5 w-5" />
                        @if ($unreadNotificationsCount > 0)
                            <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-[var(--color-teal-500)] ring-2 ring-white"></span>
                        @endif
                    </a>

                    {{-- Account Menu Dropdown --}}
                    <x-ui.dropdown align="right" width="w-56">
                        <x-slot name="trigger">
                            <button type="button" class="flex items-center gap-2 p-1 rounded-xl text-sm font-medium text-[var(--color-ink)] hover:bg-[var(--color-page)] focus:outline-none focus:ring-2 focus:ring-[var(--color-teal-600)] cursor-pointer">
                                <x-ui.avatar :name="$user->name" size="sm" />
                                <span class="max-w-[120px] truncate">{{ $user->name }}</span>
                                <x-ui.icon name="chevron-down" class="h-4 w-4 text-[var(--color-muted)]" />
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-[var(--color-line)]">
                                <p class="text-xs font-semibold text-[var(--color-ink)]">{{ $user->name }}</p>
                                <p class="text-xs text-[var(--color-muted)] truncate">{{ $user->email }}</p>
                                <span class="inline-block mt-1 text-[10px] uppercase font-bold tracking-wider text-[var(--color-teal-600)] bg-[var(--color-teal-500)]/10 px-1.5 py-0.5 rounded">
                                    {{ $user->role->value }}
                                </span>
                            </div>

                            <div class="py-1">
                                <a href="{{ $dashboardRoute }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                                    <x-ui.icon name="layout-dashboard" class="h-4 w-4 text-[var(--color-muted)]" />
                                    Dashboard
                                </a>

                                @if ($user->isCustomer())
                                    <a href="{{ route('customer.bookings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                                        <x-ui.icon name="calendar" class="h-4 w-4 text-[var(--color-muted)]" />
                                        My bookings
                                    </a>
                                    <a href="{{ route('customer.profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                                        <x-ui.icon name="user" class="h-4 w-4 text-[var(--color-muted)]" />
                                        Profile
                                    </a>
                                @elseif ($user->isProvider())
                                    <a href="{{ route('provider.bookings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                                        <x-ui.icon name="calendar" class="h-4 w-4 text-[var(--color-muted)]" />
                                        Bookings
                                    </a>
                                    <a href="{{ route('provider.services.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                                        <x-ui.icon name="briefcase" class="h-4 w-4 text-[var(--color-muted)]" />
                                        My services
                                    </a>
                                    <a href="{{ route('provider.earnings') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                                        <x-ui.icon name="wallet" class="h-4 w-4 text-[var(--color-muted)]" />
                                        Earnings
                                    </a>
                                    <a href="{{ route('provider.profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                                        <x-ui.icon name="user" class="h-4 w-4 text-[var(--color-muted)]" />
                                        Business profile
                                    </a>
                                @endif

                                <a href="{{ route('demo.sign-in') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-ink)] hover:bg-[var(--color-page)] border-t border-[var(--color-line)] mt-1 pt-2">
                                    <x-ui.icon name="rotate-ccw" class="h-4 w-4 text-[var(--color-muted)]" />
                                    Switch demo account
                                </a>

                                <form method="POST" action="{{ route('demo.sign-out') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-danger-600)] hover:bg-red-50 text-left cursor-pointer">
                                        <x-ui.icon name="log-out" class="h-4 w-4" />
                                        Log out
                                    </button>
                                </form>
                            </div>
                        </x-slot>
                    </x-ui.dropdown>
                @endauth
            </div>

            {{-- Mobile: Hamburger Menu Button --}}
            <div class="flex items-center gap-2 md:hidden">
                @auth
                    <a href="{{ route('notifications.index') }}"
                       class="relative p-2 rounded-xl text-[var(--color-muted)] hover:text-[var(--color-ink)]"
                       aria-label="Notifications">
                        <x-ui.icon name="bell" class="h-5 w-5" />
                        @if ($unreadNotificationsCount > 0)
                            <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-[var(--color-teal-500)] ring-2 ring-white"></span>
                        @endif
                    </a>
                @endauth

                <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="p-2 rounded-xl text-[var(--color-ink)] hover:bg-[var(--color-page)] focus:outline-none focus:ring-2 focus:ring-[var(--color-teal-600)] cursor-pointer"
                        aria-label="Toggle Navigation Menu">
                    <x-ui.icon name="menu" class="h-6 w-6" />
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu Panel --}}
    <div x-show="mobileMenuOpen"
         x-cloak
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         @click.outside="mobileMenuOpen = false"
         @keydown.escape.window="mobileMenuOpen = false"
         class="md:hidden border-b border-[var(--color-line)] bg-white px-4 pt-3 pb-6 space-y-3 shadow-lg">

        <nav class="space-y-1" aria-label="Mobile Navigation">
            <a href="{{ route('services.index') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                Browse services
            </a>
            <a href="{{ route('docs') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                Docs
            </a>
            <a href="{{ route('help') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                Help
            </a>
        </nav>

        <div class="border-t border-[var(--color-line)] pt-3">
            @guest
                <x-ui.button variant="secondary" size="md" :href="route('demo.sign-in')" class="w-full">
                    Log in
                </x-ui.button>
            @endguest

            @auth
                <div class="space-y-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-[var(--color-muted)]">
                        Signed in as {{ auth()->user()->name }} ({{ auth()->user()->role->value }})
                    </p>
                    <a href="{{ $dashboardRoute }}" class="block px-3 py-2 rounded-xl text-base font-medium text-[var(--color-teal-600)] hover:bg-[var(--color-page)]">
                        Dashboard
                    </a>
                    <a href="{{ route('demo.sign-in') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-[var(--color-ink)] hover:bg-[var(--color-page)]">
                        Switch demo account
                    </a>
                    <form method="POST" action="{{ route('demo.sign-out') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-base font-medium text-[var(--color-danger-600)] hover:bg-red-50 cursor-pointer">
                            Log out
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</header>
