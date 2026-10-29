<footer class="bg-white border-t border-[var(--color-line)] mt-16 text-sm text-[var(--color-muted)]">
    <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            
            {{-- Column 1: Brand & Tagline --}}
            <div class="space-y-3">
                <span class="text-lg font-bold text-[var(--color-navy-900)] block">ServiceLocal</span>
                <p class="text-sm leading-relaxed text-[var(--color-muted)]">
                    A demonstration marketplace for local services in Ireland.
                </p>
                <div class="pt-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                        Demo Mode
                    </span>
                </div>
            </div>

            {{-- Column 2: Marketplace --}}
            <div class="space-y-3">
                <h4 class="text-xs font-semibold text-[var(--color-ink)] uppercase tracking-wider">Marketplace</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('services.index') }}" class="hover:text-[var(--color-teal-600)] transition">Browse services</a></li>
                    <li><a href="{{ route('docs') }}" class="hover:text-[var(--color-teal-600)] transition">Documentation</a></li>
                    <li><a href="{{ route('help') }}" class="hover:text-[var(--color-teal-600)] transition">Help & FAQs</a></li>
                </ul>
            </div>

            {{-- Column 3: Account --}}
            <div class="space-y-3">
                <h4 class="text-xs font-semibold text-[var(--color-ink)] uppercase tracking-wider">Account</h4>
                <ul class="space-y-2 text-sm">
                    @auth
                        @php
                            $dash = match(auth()->user()->role->value) {
                                'admin' => route('admin.dashboard'),
                                'provider' => route('provider.dashboard'),
                                default => route('customer.dashboard'),
                            };
                        @endphp
                        <li><a href="{{ $dash }}" class="hover:text-[var(--color-teal-600)] transition">Dashboard</a></li>
                        <li><a href="{{ route('demo.sign-in') }}" class="hover:text-[var(--color-teal-600)] transition">Switch account</a></li>
                    @else
                        <li><a href="{{ route('demo.sign-in') }}" class="hover:text-[var(--color-teal-600)] transition">Sign in (Demo)</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Column 4: Legal & Support --}}
            <div class="space-y-3">
                <h4 class="text-xs font-semibold text-[var(--color-ink)] uppercase tracking-wider">Legal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('terms') }}" class="hover:text-[var(--color-teal-600)] transition">Terms of Service</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-[var(--color-teal-600)] transition">Privacy Policy</a></li>
                    <li><a href="{{ route('refunds') }}" class="hover:text-[var(--color-teal-600)] transition">Refund Policy</a></li>
                </ul>
            </div>
        </div>

        {{-- Bottom Disclaimer & Support --}}
        <div class="mt-12 pt-8 border-t border-[var(--color-line)] flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-[var(--color-muted)]">
            <p>
                Demo data only. Payments are simulated and no real money moves.
            </p>
            <p>
                Support: <a href="mailto:{{ config('marketplace.support_email', 'support@servicelocal.test') }}" class="hover:text-[var(--color-teal-600)] underline">{{ config('marketplace.support_email', 'support@servicelocal.test') }}</a>
            </p>
        </div>
    </div>
</footer>
