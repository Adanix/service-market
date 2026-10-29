<header class="bg-white border-b border-[var(--color-line)] sticky top-0 z-40" x-data="{ mobileMenuOpen: false }">
    {{-- Elegant Brand Accent Line --}}
    <div class="h-1 bg-gradient-to-r from-emerald-600 via-teal-500 to-slate-800"></div>

    <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            
            {{-- Left: Wordmark & Demo Badge --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight text-[var(--color-navy-900)] flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-600 rounded-lg">
                    ServiceLocal
                </a>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200"
                      title="Simulated marketplace. No real payments.">
                    Demo
                </span>
            </div>

            {{-- Center: Desktop Primary Navigation with Active Highlighting --}}
            @php
                $isServicesActive = request()->routeIs('services.*') && !request()->routeIs('provider.services.*') && !request()->routeIs('admin.services.*');
                $isDocsActive = request()->routeIs('docs');
                $isHelpActive = request()->routeIs('help');
            @endphp
            <nav class="hidden md:flex items-center gap-2" aria-label="Main Navigation">
                <a href="{{ route('services.index') }}" 
                   class="text-sm font-medium px-3.5 py-1.5 rounded-xl transition-all duration-150 {{ $isServicesActive ? 'text-emerald-800 bg-emerald-50 font-semibold border border-emerald-200 shadow-xs' : 'text-stone-700 hover:text-emerald-700 hover:bg-stone-50' }}">
                    Browse services
                </a>
                <a href="{{ route('docs') }}" 
                   class="text-sm font-medium px-3.5 py-1.5 rounded-xl transition-all duration-150 {{ $isDocsActive ? 'text-emerald-800 bg-emerald-50 font-semibold border border-emerald-200 shadow-xs' : 'text-stone-700 hover:text-emerald-700 hover:bg-stone-50' }}">
                    Docs
                </a>
                <a href="{{ route('help') }}" 
                   class="text-sm font-medium px-3.5 py-1.5 rounded-xl transition-all duration-150 {{ $isHelpActive ? 'text-emerald-800 bg-emerald-50 font-semibold border border-emerald-200 shadow-xs' : 'text-stone-700 hover:text-emerald-700 hover:bg-stone-50' }}">
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
                        $isWorkspaceActive = match($user->role->value) {
                            'admin' => request()->routeIs('admin.*'),
                            'provider' => request()->routeIs('provider.*'),
                            default => request()->routeIs('customer.*'),
                        };
                    @endphp

                    {{-- Role Dashboard Link --}}
                    <a href="{{ $dashboardRoute }}" 
                       class="text-sm font-medium px-3 py-1.5 rounded-xl transition-colors {{ $isWorkspaceActive ? 'text-emerald-800 bg-emerald-50 font-semibold border border-emerald-200' : 'text-emerald-700 hover:text-emerald-800 hover:underline' }}">
                        {{ ucfirst($user->role->value) }} workspace
                    </a>

                    {{-- Notification Bell --}}
                    <a href="{{ route('notifications.index') }}"
                       class="relative p-2 rounded-xl text-stone-500 hover:text-stone-800 hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-emerald-600 transition"
                       aria-label="Notifications ({{ $unreadNotificationsCount }} unread)">
                        <x-ui.icon name="bell" class="h-5 w-5" />
                        @if ($unreadNotificationsCount > 0)
                            <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        @endif
                    </a>

                    {{-- Account Menu Dropdown --}}
                    <x-ui.dropdown align="right" width="w-64">
                        <x-slot name="trigger">
                            <button type="button" class="flex items-center gap-2 p-1 rounded-xl text-sm font-medium text-stone-800 hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-emerald-600 cursor-pointer">
                                <x-ui.avatar :name="$user->name" size="sm" />
                                <span class="max-w-[120px] truncate">{{ $user->name }}</span>
                                <x-ui.icon name="chevron-down" class="h-4 w-4 text-stone-400" />
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2.5 border-b border-stone-200 bg-stone-50/60 rounded-t-2xl">
                                <p class="text-xs font-bold text-stone-900">{{ $user->name }}</p>
                                <p class="text-xs text-stone-500 truncate">{{ $user->email }}</p>
                                <span class="inline-block mt-1 text-[10px] uppercase font-bold tracking-wider text-emerald-800 bg-emerald-100 border border-emerald-200 px-2 py-0.5 rounded">
                                    {{ $user->role->value }}
                                </span>
                            </div>

                            @php
                                $checkActive = fn($routes) => request()->routeIs($routes);
                            @endphp

                            <div class="py-1">
                                {{-- Customer Dropdown Menu --}}
                                @if ($user->isCustomer())
                                    @php
                                        $dashActive = $checkActive('customer.dashboard');
                                        $bookingsActive = $checkActive('customer.bookings.*');
                                        $profileActive = $checkActive('customer.profile.*');
                                    @endphp
                                    <a href="{{ route('customer.dashboard') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $dashActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="layout-dashboard" class="h-4 w-4 {{ $dashActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Dashboard
                                        </span>
                                        @if($dashActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('customer.bookings.index') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $bookingsActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="calendar" class="h-4 w-4 {{ $bookingsActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            My bookings
                                        </span>
                                        @if($bookingsActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('customer.profile.edit') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $profileActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="user" class="h-4 w-4 {{ $profileActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Profile settings
                                        </span>
                                        @if($profileActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>

                                {{-- Provider Dropdown Menu --}}
                                @elseif ($user->isProvider())
                                    @php
                                        $dashActive = $checkActive('provider.dashboard');
                                        $bookingsActive = $checkActive('provider.bookings.*');
                                        $servicesActive = $checkActive('provider.services.*');
                                        $earningsActive = $checkActive('provider.earnings');
                                        $profileActive = $checkActive('provider.profile.*');
                                    @endphp
                                    <a href="{{ route('provider.dashboard') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $dashActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="layout-dashboard" class="h-4 w-4 {{ $dashActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Dashboard
                                        </span>
                                        @if($dashActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('provider.bookings.index') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $bookingsActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="calendar" class="h-4 w-4 {{ $bookingsActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Bookings
                                        </span>
                                        @if($bookingsActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('provider.services.index') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $servicesActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="briefcase" class="h-4 w-4 {{ $servicesActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            My services
                                        </span>
                                        @if($servicesActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('provider.earnings') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $earningsActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="coins" class="h-4 w-4 {{ $earningsActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Earnings & Payouts
                                        </span>
                                        @if($earningsActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('provider.profile.edit') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $profileActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="user" class="h-4 w-4 {{ $profileActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Business profile
                                        </span>
                                        @if($profileActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>

                                {{-- Admin Dropdown Menu --}}
                                @elseif ($user->isAdmin())
                                    @php
                                        $dashActive = $checkActive('admin.dashboard');
                                        $bookingsActive = $checkActive('admin.bookings.*');
                                        $providersActive = $checkActive('admin.providers.*');
                                        $servicesActive = $checkActive('admin.services.*');
                                        $paymentsActive = $checkActive(['admin.payments.*', 'admin.payouts.*']);
                                        $usersActive = $checkActive('admin.users.*');
                                        $settingsActive = $checkActive('admin.settings.*');
                                        $auditActive = $checkActive('admin.audit.*');
                                    @endphp
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $dashActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="layout-dashboard" class="h-4 w-4 {{ $dashActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Dashboard
                                        </span>
                                        @if($dashActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.bookings.index') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $bookingsActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="calendar" class="h-4 w-4 {{ $bookingsActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Bookings oversight
                                        </span>
                                        @if($bookingsActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.providers.index') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $providersActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="shield-check" class="h-4 w-4 {{ $providersActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Provider verification
                                        </span>
                                        @if($providersActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.services.index') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $servicesActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="briefcase" class="h-4 w-4 {{ $servicesActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Service moderation
                                        </span>
                                        @if($servicesActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.payments.index') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $paymentsActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="credit-card" class="h-4 w-4 {{ $paymentsActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Payments & Payouts
                                        </span>
                                        @if($paymentsActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $usersActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="user" class="h-4 w-4 {{ $usersActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Users directory
                                        </span>
                                        @if($usersActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.settings.edit') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $settingsActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="settings" class="h-4 w-4 {{ $settingsActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Platform settings
                                        </span>
                                        @if($settingsActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.audit.index') }}" 
                                       class="flex items-center justify-between px-4 py-2 text-sm transition-colors {{ $auditActive ? 'bg-emerald-50 text-emerald-900 font-semibold border-l-2 border-emerald-600' : 'text-stone-700 hover:bg-stone-50 hover:text-stone-900' }}">
                                        <span class="flex items-center gap-2.5">
                                            <x-ui.icon name="activity" class="h-4 w-4 {{ $auditActive ? 'text-emerald-600' : 'text-stone-400' }}" />
                                            Audit trail
                                        </span>
                                        @if($auditActive)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                        @endif
                                    </a>
                                @endif

                                <div class="border-t border-stone-200 mt-1 pt-1">
                                    <a href="{{ route('demo.sign-in') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-stone-700 hover:bg-stone-50 hover:text-stone-900">
                                        <x-ui.icon name="rotate-ccw" class="h-4 w-4 text-stone-400" />
                                        Switch demo account
                                    </a>

                                    <form method="POST" action="{{ route('demo.sign-out') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 text-left cursor-pointer font-medium">
                                            <x-ui.icon name="log-out" class="h-4 w-4" />
                                            Log out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-ui.dropdown>
                @endauth
            </div>

            {{-- Mobile: Hamburger Menu Button --}}
            <div class="flex items-center gap-2 md:hidden">
                @auth
                    <a href="{{ route('notifications.index') }}"
                       class="relative p-2 rounded-xl text-stone-500 hover:text-stone-800"
                       aria-label="Notifications">
                        <x-ui.icon name="bell" class="h-5 w-5" />
                        @if ($unreadNotificationsCount > 0)
                            <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                        @endif
                    </a>
                @endauth

                <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="p-2 rounded-xl text-stone-800 hover:bg-stone-50 focus:outline-none focus:ring-2 focus:ring-emerald-600 cursor-pointer"
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
         class="md:hidden border-b border-stone-200 bg-white px-4 pt-3 pb-6 space-y-3 shadow-lg">

        <nav class="space-y-1" aria-label="Mobile Navigation">
            <a href="{{ route('services.index') }}" 
               class="block px-3 py-2 rounded-xl text-base font-medium transition {{ $isServicesActive ? 'text-emerald-800 bg-emerald-50 font-semibold border border-emerald-200' : 'text-stone-700 hover:bg-stone-50' }}">
                Browse services
            </a>
            <a href="{{ route('docs') }}" 
               class="block px-3 py-2 rounded-xl text-base font-medium transition {{ $isDocsActive ? 'text-emerald-800 bg-emerald-50 font-semibold border border-emerald-200' : 'text-stone-700 hover:bg-stone-50' }}">
                Docs
            </a>
            <a href="{{ route('help') }}" 
               class="block px-3 py-2 rounded-xl text-base font-medium transition {{ $isHelpActive ? 'text-emerald-800 bg-emerald-50 font-semibold border border-emerald-200' : 'text-stone-700 hover:bg-stone-50' }}">
                Help
            </a>
        </nav>

        <div class="border-t border-stone-200 pt-3">
            @guest
                <x-ui.button variant="secondary" size="md" :href="route('demo.sign-in')" class="w-full">
                    Log in
                </x-ui.button>
            @endguest

            @auth
                <div class="space-y-1">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider text-stone-500">
                        Signed in as {{ auth()->user()->name }} ({{ auth()->user()->role->value }})
                    </p>
                    <a href="{{ $dashboardRoute }}" 
                       class="block px-3 py-2 rounded-xl text-base font-medium transition {{ $isWorkspaceActive ? 'text-emerald-800 bg-emerald-50 font-semibold border border-emerald-200' : 'text-emerald-700 hover:bg-stone-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('demo.sign-in') }}" class="block px-3 py-2 rounded-xl text-base font-medium text-stone-700 hover:bg-stone-50">
                        Switch demo account
                    </a>
                    <form method="POST" action="{{ route('demo.sign-out') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-base font-medium text-rose-600 hover:bg-rose-50 cursor-pointer">
                            Log out
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</header>
