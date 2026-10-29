<x-layouts.app title="Help & Support - ServiceLocal">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold text-emerald-700 tracking-wider uppercase">Support Centre</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 mt-2">How can we assist you?</h1>
            <p class="text-stone-600 mt-2 text-sm">Find answers to common questions about booking, payments, and provider requirements.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
            {{-- FAQ Section (8 items) --}}
            <div class="md:col-span-7 space-y-4">
                <h2 class="text-xl font-bold text-stone-900 mb-4">Frequently Asked Questions</h2>

                <details class="group bg-white p-4 rounded-xl border border-stone-200 open:border-emerald-500 transition-colors">
                    <summary class="font-semibold text-stone-900 cursor-pointer list-none flex justify-between items-center text-sm">
                        <span>When will my card be charged?</span>
                        <x-ui.icon name="chevron-down" class="w-4 h-4 text-stone-400 group-open:rotate-180 transition-transform" />
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Your payment card is not charged upfront. An authorization hold is placed when you submit your booking request. Funds are only captured once the provider has completed the service and you confirm completion (or the auto-complete window expires).
                    </p>
                </details>

                <details class="group bg-white p-4 rounded-xl border border-stone-200 open:border-emerald-500 transition-colors">
                    <summary class="font-semibold text-stone-900 cursor-pointer list-none flex justify-between items-center text-sm">
                        <span>What happens if a provider declines my booking?</span>
                        <x-ui.icon name="chevron-down" class="w-4 h-4 text-stone-400 group-open:rotate-180 transition-transform" />
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        If a provider cannot accommodate your request, they will provide a decline reason and the payment authorization hold on your card is released immediately. No funds leave your account.
                    </p>
                </details>

                <details class="group bg-white p-4 rounded-xl border border-stone-200 open:border-emerald-500 transition-colors">
                    <summary class="font-semibold text-stone-900 cursor-pointer list-none flex justify-between items-center text-sm">
                        <span>Can I cancel a booking after it is accepted?</span>
                        <x-ui.icon name="chevron-down" class="w-4 h-4 text-stone-400 group-open:rotate-180 transition-transform" />
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Yes. Customers can cancel bookings in Pending Provider, Accepted, or In Progress states with a mandatory cancellation reason. The payment authorization will be released.
                    </p>
                </details>

                <details class="group bg-white p-4 rounded-xl border border-stone-200 open:border-emerald-500 transition-colors">
                    <summary class="font-semibold text-stone-900 cursor-pointer list-none flex justify-between items-center text-sm">
                        <span>How does the auto-complete confirmation window work?</span>
                        <x-ui.icon name="chevron-down" class="w-4 h-4 text-stone-400 group-open:rotate-180 transition-transform" />
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        When a provider marks a job complete, you receive a notification to verify and confirm. If you take no action within the configured auto-complete window (typically 72 hours), the system automatically confirms the completion and captures the payment.
                    </p>
                </details>

                <details class="group bg-white p-4 rounded-xl border border-stone-200 open:border-emerald-500 transition-colors">
                    <summary class="font-semibold text-stone-900 cursor-pointer list-none flex justify-between items-center text-sm">
                        <span>Are all service providers verified?</span>
                        <x-ui.icon name="chevron-down" class="w-4 h-4 text-stone-400 group-open:rotate-180 transition-transform" />
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Yes. All providers on ServiceLocal must register their business profile, verify contact information and service county, and receive explicit administrator approval before any of their services can be listed.
                    </p>
                </details>

                <details class="group bg-white p-4 rounded-xl border border-stone-200 open:border-emerald-500 transition-colors">
                    <summary class="font-semibold text-stone-900 cursor-pointer list-none flex justify-between items-center text-sm">
                        <span>What currency is supported?</span>
                        <x-ui.icon name="chevron-down" class="w-4 h-4 text-stone-400 group-open:rotate-180 transition-transform" />
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        All transactions on ServiceLocal are processed in Euro (€ / EUR). Pricing is transparent with no hidden booking fees for customers.
                    </p>
                </details>

                <details class="group bg-white p-4 rounded-xl border border-stone-200 open:border-emerald-500 transition-colors">
                    <summary class="font-semibold text-stone-900 cursor-pointer list-none flex justify-between items-center text-sm">
                        <span>How do providers receive payouts?</span>
                        <x-ui.icon name="chevron-down" class="w-4 h-4 text-stone-400 group-open:rotate-180 transition-transform" />
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Completed bookings are batched by the platform administration into periodic payouts, minus the marketplace commission fee. Providers can review their gross, commission, and net payouts on their Earnings dashboard.
                    </p>
                </details>

                <details class="group bg-white p-4 rounded-xl border border-stone-200 open:border-emerald-500 transition-colors">
                    <summary class="font-semibold text-stone-900 cursor-pointer list-none flex justify-between items-center text-sm">
                        <span>How do I test this demo application?</span>
                        <x-ui.icon name="chevron-down" class="w-4 h-4 text-stone-400 group-open:rotate-180 transition-transform" />
                    </summary>
                    <p class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        Visit the Demo Account Switcher (/demo/sign-in) to instantly switch between customer, provider, and administrator roles without passwords. Test payment card numbers are provided directly on checkout and documentation pages.
                    </p>
                </details>
            </div>

            {{-- Contact Form --}}
            <div class="md:col-span-5">
                <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
                    <h2 class="text-lg font-bold text-stone-900 mb-1">Contact Support</h2>
                    <p class="text-xs text-stone-500 mb-6">Send an inquiry directly to the marketplace team.</p>

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <x-ui.input name="name" label="Full Name" required :value="old('name', auth()->user()?->name)" />
                        <x-ui.input name="email" label="Email Address" type="email" required :value="old('email', auth()->user()?->email)" />
                        <x-ui.input name="subject" label="Subject" required :value="old('subject')" />
                        <x-ui.textarea name="message" label="Your Message" rows="4" required>{{ old('message') }}</x-ui.textarea>

                        <x-ui.button type="submit" variant="primary" class="w-full">
                            Send Message
                        </x-ui.button>
                    </form>

                    <div class="mt-6 pt-4 border-t border-stone-100 text-center">
                        <span class="text-xs text-stone-500">Or email us directly at:</span>
                        <a href="mailto:{{ $supportEmail }}" class="block text-xs font-semibold text-emerald-700 hover:underline mt-0.5">{{ $supportEmail }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
