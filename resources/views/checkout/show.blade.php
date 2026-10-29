<x-layouts.app :title="'Book ' . $service->title . ' - Checkout'">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
        cardNumber: '',
        cardExpiry: '12/28',
        cardCvc: '123',
        setCard(num) {
            this.cardNumber = num;
            this.cardExpiry = '12/28';
            this.cardCvc = '123';
        }
    }">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Services', 'url' => route('services.index')],
            ['label' => $service->title, 'url' => route('services.show', $service->slug)],
            ['label' => 'Checkout'],
        ]" class="mb-6" />

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight">Booking Checkout</h1>
            <p class="text-sm text-stone-500 mt-1">Review your service booking and authorize payment hold.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- Left: Checkout Form --}}
            <div class="lg:col-span-7 space-y-6">
                {{-- Payment Disclosure Alert --}}
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-start gap-3">
                    <x-ui.icon name="shield-check" class="w-5 h-5 text-emerald-700 shrink-0 mt-0.5" />
                    <div>
                        <span class="font-bold block text-sm mb-0.5">Zero Upfront Charge</span>
                        Your card will not be charged today. We place a temporary authorization hold. Funds are only captured after the service is marked complete and confirmed.
                    </div>
                </div>

                {{-- Booking Form --}}
                <form action="{{ route('checkout.process', $service->slug) }}" method="POST" class="bg-white p-6 sm:p-7 rounded-2xl border border-stone-200 shadow-sm space-y-6">
                    @csrf
                    <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}">

                    <div>
                        <h2 class="text-base font-bold text-stone-900 mb-4 pb-2 border-b border-stone-100 flex items-center gap-2">
                            <x-ui.icon name="calendar" class="w-4 h-4 text-emerald-600" />
                            Schedule & Requirements
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label for="preferred_start_at" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                    Preferred Date & Time <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local"
                                       name="preferred_start_at"
                                       id="preferred_start_at"
                                       required
                                       min="{{ $earliestTime->format('Y-m-d\TH:i') }}"
                                       max="{{ $latestDate->format('Y-m-d\TH:i') }}"
                                       value="{{ old('preferred_start_at', $earliestTime->addDay()->format('Y-m-d\T10:00')) }}"
                                       class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('preferred_start_at') border-red-500 @enderror">
                                @error('preferred_start_at')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-[11px] text-stone-400">Must be at least 24 hours in advance and within 90 days.</p>
                            </div>

                            <div>
                                <label for="customer_note" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                    Notes for Provider (Optional)
                                </label>
                                <textarea name="customer_note"
                                          id="customer_note"
                                          rows="3"
                                          placeholder="Specific instructions, parking access, or special requests..."
                                          class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('customer_note') border-red-500 @enderror">{{ old('customer_note') }}</textarea>
                                @error('customer_note')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-stone-900 mb-2 pb-2 border-b border-stone-100 flex items-center gap-2">
                            <x-ui.icon name="credit-card" class="w-4 h-4 text-emerald-600" />
                            Simulated Payment Authorization
                        </h2>

                        {{-- Quick-fill Test Card Selector --}}
                        <div class="mb-4">
                            <span class="text-xs text-stone-500 block mb-2 font-medium">Quick-Fill Demo Test Cards:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($demoCards as $number => $card)
                                    <button type="button"
                                            @click="setCard('{{ $number }}')"
                                            class="text-left p-2 rounded-lg border border-stone-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition-colors text-xs">
                                        <div class="font-bold text-stone-800">{{ strtoupper($card['brand']) }} &bull; {{ $card['success'] ? 'Valid' : ($card['failure_code'] ?? 'Declined') }}</div>
                                        <div class="font-mono text-stone-500 text-[11px]">{{ chunk_split($number, 4, ' ') }}</div>
                                        <div class="text-[10px] text-stone-400">Outcome: {{ $card['success'] ? 'Authorizes' : ($card['failure_code'] ?? 'Declined') }}</div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label for="cardholder_name" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                    Cardholder Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="cardholder_name"
                                       id="cardholder_name"
                                       required
                                       value="{{ old('cardholder_name', auth()->user()->name) }}"
                                       placeholder="Name as it appears on card"
                                       class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('cardholder_name') border-red-500 @enderror">
                                @error('cardholder_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="card_number" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                    Card Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="card_number"
                                       id="card_number"
                                       required
                                       x-model="cardNumber"
                                       placeholder="4242 4242 4242 4242"
                                       class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg font-mono text-sm text-stone-800 placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('card_number') border-red-500 @enderror">
                                @error('card_number')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="card_expiry" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                        Expiry (MM/YY) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="card_expiry"
                                           id="card_expiry"
                                           required
                                           x-model="cardExpiry"
                                           placeholder="MM/YY"
                                           maxlength="5"
                                           class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg font-mono text-sm text-stone-800 placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('card_expiry') border-red-500 @enderror">
                                    @error('card_expiry')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="card_cvc" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                        CVC <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="card_cvc"
                                           id="card_cvc"
                                           required
                                           x-model="cardCvc"
                                           placeholder="123"
                                           maxlength="4"
                                           class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg font-mono text-sm text-stone-800 placeholder-stone-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('card_cvc') border-red-500 @enderror">
                                    @error('card_cvc')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-stone-100 space-y-4">
                        <div class="flex items-start gap-2.5">
                            <input type="checkbox"
                                   name="accept_terms"
                                   id="accept_terms"
                                   value="1"
                                   required
                                   @checked(old('accept_terms', true))
                                   class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 border-stone-300">
                            <label for="accept_terms" class="text-xs text-stone-600 cursor-pointer">
                                I agree to the <a href="{{ route('terms') }}" target="_blank" class="text-emerald-700 underline font-semibold">Terms of Service</a> and <a href="{{ route('refunds') }}" target="_blank" class="text-emerald-700 underline font-semibold">Cancellation Policy</a>.
                            </label>
                        </div>
                        @error('accept_terms')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <x-ui.button type="submit" variant="primary" class="w-full py-3 text-base font-bold shadow-md">
                            Authorize & Request Booking
                        </x-ui.button>
                        <p class="text-[11px] text-stone-400 text-center mt-2.5">
                            By clicking Authorize, you agree to ServiceLocal Terms of Service and Refund Policy.
                        </p>
                    </div>
                </form>
            </div>

            {{-- Right: Order Summary --}}
            <div class="lg:col-span-5">
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-5">
                    <h2 class="text-lg font-bold text-stone-900 pb-3 border-b border-stone-100">Order Summary</h2>

                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                {{ $service->category->name }}
                            </span>
                            <x-marketplace.county-badge :county="$service->county" />
                        </div>
                        <h3 class="text-lg font-bold text-stone-900">{{ $service->title }}</h3>
                        <p class="text-xs text-stone-500 mt-1">Provider: <strong>{{ $service->providerProfile->business_name }}</strong></p>
                    </div>

                    <div class="py-3 border-y border-stone-100 space-y-2 text-xs text-stone-600">
                        <div class="flex justify-between">
                            <span>Estimated Duration</span>
                            <span class="font-bold text-stone-800">{{ $service->duration_minutes }} minutes</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Service Location</span>
                            <span class="font-bold text-stone-800">{{ $service->county->name }}, Ireland</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Customer</span>
                            <span class="font-bold text-stone-800">{{ auth()->user()->name }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-baseline pt-2">
                        <div>
                            <span class="text-sm font-bold text-stone-900 block">Total Due</span>
                            <span class="text-[11px] text-stone-400">Captured only upon completion</span>
                        </div>
                        <x-ui.money :cents="$service->price_cents" class="text-2xl font-extrabold text-stone-900" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
