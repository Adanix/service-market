<x-layouts.app title="Service Moderation - Admin">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ rejectModalOpen: false, currentServiceId: null, currentServiceTitle: '' }">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Service Moderation</h1>
                <p class="text-sm text-stone-500 mt-1">Review, approve, or reject provider service listings.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm mb-6">
            <form action="{{ route('admin.services.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search title or provider..." class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <select name="status" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Statuses</option>
                    <option value="pending_review" @selected(($filters['status'] ?? '') === 'pending_review')>Pending Review</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                    <option value="rejected" @selected(($filters['status'] ?? '') === 'rejected')>Rejected</option>
                    <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draft</option>
                    <option value="archived" @selected(($filters['status'] ?? '') === 'archived')>Archived</option>
                </select>

                <select name="category_id" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(($filters['category_id'] ?? '') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="county_id" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Counties</option>
                    @foreach($counties as $co)
                        <option value="{{ $co->id }}" @selected(($filters['county_id'] ?? '') == $co->id)>{{ $co->name }}</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" variant="primary" class="w-full text-xs">
                        Filter
                    </x-ui.button>
                    <a href="{{ route('admin.services.index') }}" class="px-3 py-2 text-xs text-stone-500 hover:text-stone-800">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead class="bg-stone-50 text-stone-600 font-semibold text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5 text-left">Service</th>
                            <th class="px-5 py-3.5 text-left">Provider</th>
                            <th class="px-5 py-3.5 text-left">Category</th>
                            <th class="px-5 py-3.5 text-left">Status</th>
                            <th class="px-5 py-3.5 text-right">Price</th>
                            <th class="px-5 py-3.5 text-right">Moderation Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @foreach($services as $svc)
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-stone-900 text-xs">{{ $svc->title }}</div>
                                    <div class="text-[11px] text-stone-400">Duration: {{ $svc->duration_minutes }}m &bull; County: {{ $svc->county->name }}</div>
                                </td>
                                <td class="px-5 py-4 text-xs text-stone-700 font-medium">
                                    {{ $svc->providerProfile->business_name }}
                                </td>
                                <td class="px-5 py-4 text-xs text-stone-600">
                                    {{ $svc->category->name }}
                                </td>
                                <td class="px-5 py-4">
                                    <x-marketplace.status-badge :status="$svc->status->value" />
                                </td>
                                <td class="px-5 py-4 text-right font-bold text-stone-900 text-xs">
                                    <x-ui.money :cents="$svc->price_cents" />
                                </td>
                                <td class="px-5 py-4 text-right space-x-2">
                                    @if($svc->status->value === 'pending_review')
                                        <form action="{{ route('admin.services.approve', $svc->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                                                Approve
                                            </button>
                                        </form>

                                        <button type="button"
                                                @click="rejectModalOpen = true; currentServiceId = {{ $svc->id }}; currentServiceTitle = '{{ addslashes($svc->title) }}'"
                                                class="text-xs font-semibold text-red-600 hover:text-red-800">
                                            Reject
                                        </button>
                                    @endif

                                    @if($svc->status->value === 'active')
                                        <form action="{{ route('admin.services.archive', $svc->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Archive this active service?');">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-stone-500 hover:text-stone-800">
                                                Archive
                                            </button>
                                        </form>
                                    @endif

                                    @if($svc->status->value === 'rejected' && $svc->moderation_note)
                                        <span class="text-[11px] text-amber-700 italic block" title="{{ $svc->moderation_note }}">
                                            Note recorded
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-200">
                {{ $services->links() }}
            </div>
        </div>

        {{-- Reject Modal --}}
        <div x-show="rejectModalOpen"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="rejectModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200">
                <h3 class="text-lg font-bold text-stone-900 mb-2">Reject Service Listing</h3>
                <p class="text-xs text-stone-600 mb-4">
                    Rejecting <strong x-text="currentServiceTitle"></strong>. Provide constructive feedback for the provider.
                </p>

                <form :action="'/admin/services/' + currentServiceId + '/reject'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="note" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1.5">
                            Moderation Note <span class="text-red-500">*</span>
                        </label>
                        <textarea name="note" id="note" rows="3" required placeholder="Specify what needs to be changed (e.g. Inclusions vague, price too low)..." class="block w-full px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-sm text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 text-xs font-medium text-stone-600 hover:text-stone-800">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold text-xs rounded-lg shadow">
                            Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
