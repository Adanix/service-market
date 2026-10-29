<x-layouts.app title="Marketplace Settings - Admin">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs :crumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Admin Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Platform Settings'],
        ]" class="mb-6" />

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Platform Configuration</h1>
            <p class="text-sm text-stone-500 mt-1">Configure marketplace fees, escrow authorization windows, and scheduling rules.</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-200 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2 mb-4">General Platform Information</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="marketplace_name" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                Marketplace Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="marketplace_name" id="marketplace_name" required value="{{ old('marketplace_name', $settings['marketplace_name']) }}" class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label for="support_email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                Support Contact Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="support_email" id="support_email" required value="{{ old('support_email', $settings['support_email']) }}" class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2 mb-4">Financial & Escrow Parameters</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="platform_fee_bps" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                Platform Fee (Basis Points) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="platform_fee_bps" id="platform_fee_bps" required min="0" max="5000" value="{{ old('platform_fee_bps', $settings['platform_fee_bps']) }}" class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="mt-1 text-[11px] text-stone-400">1500 bps = 15.00% commission deducted from provider gross.</p>
                        </div>

                        <div>
                            <label for="authorization_days" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                Authorization Hold Window (Days) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="authorization_days" id="authorization_days" required min="1" max="14" value="{{ old('authorization_days', $settings['authorization_days']) }}" class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="mt-1 text-[11px] text-stone-400">Uncompleted bookings expire automatically after this hold period.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2 mb-4">Scheduling & Confirmation Timing</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="auto_complete_hours" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                Auto-Complete (Hours) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="auto_complete_hours" id="auto_complete_hours" required min="24" max="168" value="{{ old('auto_complete_hours', $settings['auto_complete_hours']) }}" class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="mt-1 text-[11px] text-stone-400">Hours client has to confirm before auto-capture.</p>
                        </div>

                        <div>
                            <label for="min_notice_hours" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                Minimum Notice (Hours) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="min_notice_hours" id="min_notice_hours" required min="0" max="168" value="{{ old('min_notice_hours', $settings['min_notice_hours']) }}" class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="mt-1 text-[11px] text-stone-400">Earliest time slot client can book from now.</p>
                        </div>

                        <div>
                            <label for="max_advance_days" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                                Max Advance (Days) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="max_advance_days" id="max_advance_days" required min="7" max="365" value="{{ old('max_advance_days', $settings['max_advance_days']) }}" class="block w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <p class="mt-1 text-[11px] text-stone-400">Furthest future booking allowed.</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="reminder_hours" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Reminder Notice (Hours) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="reminder_hours" id="reminder_hours" required min="1" max="72" value="{{ old('reminder_hours', $settings['reminder_hours'] ?? 24) }}" class="block w-full sm:w-1/3 px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2.5 text-xs font-semibold text-stone-600 hover:text-stone-900">
                    Cancel
                </a>
                <x-ui.button type="submit" variant="primary">
                    Save Marketplace Settings
                </x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.app>
