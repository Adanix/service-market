@extends('layouts.auth')

@section('title', 'Choose a demo account')

@section('content')
<div class="space-y-8">
    <div class="text-center max-w-2xl mx-auto space-y-3">
        <h1 class="text-3xl font-bold tracking-tight text-[var(--color-navy-900)]">
            Choose a demo account
        </h1>
        <p class="text-base text-[var(--color-muted)] leading-relaxed">
            This demo has no passwords. Pick an account to explore the marketplace as a customer, a provider, or an administrator.
        </p>

        @auth
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900 mt-2">
                <x-ui.icon name="info" class="h-4 w-4 text-blue-700 shrink-0" />
                <span>You are signed in as <strong>{{ auth()->user()->name }}</strong>. Choosing another account will switch the session.</span>
            </div>
        @endauth
    </div>

    @if ($customers->isEmpty() && $providers->isEmpty() && $admins->isEmpty())
        <x-ui.empty-state title="No demo accounts are available." icon="users">
            <p class="text-sm text-[var(--color-muted)] mt-1">
                Run <code>php artisan demo:reset</code> to restore the demo data.
            </p>
        </x-ui.empty-state>
    @else
        {{-- 1. Customers --}}
        <section class="space-y-4">
            <div class="border-b border-[var(--color-line)] pb-2 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-[var(--color-navy-900)]">Customers</h2>
                <span class="text-xs text-[var(--color-muted)]">Browse, book, and confirm service completion</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($customers as $customer)
                    @php
                        $firstName = explode(' ', $customer->name)[0];
                        $isActive = $customer->isActive();
                    @endphp
                    <div class="rounded-2xl bg-white border border-[var(--color-line)] p-5 shadow-xs flex flex-col justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <x-ui.avatar :name="$customer->name" size="md" />
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-[var(--color-ink)] truncate">{{ $customer->name }}</h3>
                                <p class="text-xs text-[var(--color-muted)] truncate">{{ $customer->email }}</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        Customer
                                    </span>
                                    @if ($customer->county)
                                        <x-marketplace.county-badge :county="$customer->county" />
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            @if ($isActive)
                                <form method="POST" action="{{ route('demo.sign-in.as', $customer) }}">
                                    @csrf
                                    <x-ui.button variant="secondary" size="sm" type="submit" class="w-full">
                                        Sign in as {{ $firstName }}
                                    </x-ui.button>
                                </form>
                            @else
                                <x-ui.button variant="secondary" size="sm" disabled class="w-full">
                                    Sign in as {{ $firstName }}
                                </x-ui.button>
                                <p class="mt-1 text-center text-xs text-[var(--color-danger-600)]">This demo account is not active.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- 2. Providers --}}
        <section class="space-y-4 pt-4">
            <div class="border-b border-[var(--color-line)] pb-2 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-[var(--color-navy-900)]">Providers</h2>
                <span class="text-xs text-[var(--color-muted)]">Manage listings, accept bookings, and track earnings</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($providers as $provider)
                    @php
                        $firstName = explode(' ', $provider->name)[0];
                        $profile = $provider->providerProfile;
                        $isActive = $provider->isActive();
                        $isApproved = $profile && $profile->isApproved();
                        $isSuspended = $provider->isSuspended() || ($profile && $profile->isSuspended());
                    @endphp
                    <div class="rounded-2xl bg-white border border-[var(--color-line)] p-5 shadow-xs flex flex-col justify-between gap-4 {{ $isSuspended ? 'bg-red-50/20' : '' }}">
                        <div class="flex items-start gap-3">
                            <x-ui.avatar :name="$provider->name" size="md" />
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="text-sm font-semibold text-[var(--color-ink)] truncate">{{ $provider->name }}</h3>
                                    @if ($isSuspended)
                                        <x-ui.badge variant="danger">Suspended</x-ui.badge>
                                    @elseif (!$isApproved)
                                        <x-ui.badge variant="warning">Pending approval</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="success">Approved</x-ui.badge>
                                    @endif
                                </div>
                                <p class="text-xs text-[var(--color-muted)] truncate">{{ $provider->email }}</p>

                                @if ($profile)
                                    <p class="text-xs font-medium text-[var(--color-ink)] mt-1.5">{{ $profile->business_name }}</p>
                                    <div class="mt-1.5 flex items-center gap-2">
                                        <x-marketplace.county-badge :county="$profile->county ?? $provider->county" />
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            @if ($isActive && $isApproved)
                                <form method="POST" action="{{ route('demo.sign-in.as', $provider) }}">
                                    @csrf
                                    <x-ui.button variant="secondary" size="sm" type="submit" class="w-full">
                                        Sign in as {{ $firstName }}
                                    </x-ui.button>
                                </form>
                            @else
                                <x-ui.button variant="secondary" size="sm" disabled class="w-full">
                                    Sign in as {{ $firstName }}
                                </x-ui.button>
                                <p class="mt-1 text-center text-xs text-[var(--color-danger-600)]">
                                    @if ($isSuspended)
                                        This demo account is suspended.
                                    @else
                                        This demo account is not active.
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- 3. Administrators --}}
        <section class="space-y-4 pt-4">
            <div class="border-b border-[var(--color-line)] pb-2 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-[var(--color-navy-900)]">Administrators</h2>
                <span class="text-xs text-[var(--color-muted)]">Moderate services, manage payouts, and supervise marketplace</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($admins as $admin)
                    @php
                        $firstName = explode(' ', $admin->name)[0];
                    @endphp
                    <div class="rounded-2xl bg-white border border-[var(--color-line)] p-5 shadow-xs flex flex-col justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <x-ui.avatar :name="$admin->name" size="md" />
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-[var(--color-ink)] truncate">{{ $admin->name }}</h3>
                                <p class="text-xs text-[var(--color-muted)] truncate">{{ $admin->email }}</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        Administrator
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <form method="POST" action="{{ route('demo.sign-in.as', $admin) }}">
                                @csrf
                                <x-ui.button variant="secondary" size="sm" type="submit" class="w-full">
                                    Sign in as {{ $firstName }}
                                </x-ui.button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="pt-6 border-t border-[var(--color-line)] text-center text-xs text-[var(--color-muted)]">
        All data is fictional. Payments are simulated and no real money moves.
    </div>
</div>
@endsection
