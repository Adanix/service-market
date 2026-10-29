<x-layouts.app title="My Services - ServiceLocal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Service Catalog</h1>
                <p class="text-sm text-stone-500 mt-1">Manage your service offerings, pricing, and moderation status.</p>
            </div>
            <div>
                <a href="{{ route('provider.services.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-xs shadow transition-colors">
                    <x-ui.icon name="plus" class="w-4 h-4" />
                    <span>Create New Service</span>
                </a>
            </div>
        </div>

        {{-- Status Filter Tabs --}}
        <div class="border-b border-stone-200 mb-6">
            <nav class="flex space-x-6 text-sm font-medium">
                <a href="{{ route('provider.services.index') }}" class="pb-3 border-b-2 {{ empty($status) ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    All Services
                </a>
                <a href="{{ route('provider.services.index', ['status' => 'active']) }}" class="pb-3 border-b-2 {{ $status === 'active' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    Active
                </a>
                <a href="{{ route('provider.services.index', ['status' => 'pending_review']) }}" class="pb-3 border-b-2 {{ $status === 'pending_review' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    Pending Review
                </a>
                <a href="{{ route('provider.services.index', ['status' => 'draft']) }}" class="pb-3 border-b-2 {{ $status === 'draft' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    Drafts
                </a>
                <a href="{{ route('provider.services.index', ['status' => 'rejected']) }}" class="pb-3 border-b-2 {{ $status === 'rejected' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    Rejected
                </a>
            </nav>
        </div>

        {{-- Services List --}}
        @if($services->isEmpty())
            <x-ui.empty-state
                title="No services found"
                description="You don't have any service listings matching this filter."
                icon="package"
            >
                <div class="mt-4">
                    <a href="{{ route('provider.services.create') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                        Create your first service
                    </a>
                </div>
            </x-ui.empty-state>
        @else
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                <div class="divide-y divide-stone-100">
                    @foreach($services as $service)
                        <div class="p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-stone-50/50 transition-colors">
                            <div class="space-y-2 max-w-2xl">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider bg-emerald-50 px-2 py-0.5 rounded">
                                        {{ $service->category->name }}
                                    </span>
                                    <x-marketplace.status-badge :status="$service->status->value" />
                                    <span class="text-xs text-stone-400">Duration: {{ $service->duration_minutes }}m</span>
                                </div>

                                <h2 class="text-base font-bold text-stone-900">
                                    {{ $service->title }}
                                </h2>

                                <p class="text-xs text-stone-600 line-clamp-2">
                                    {{ $service->description }}
                                </p>

                                @if($service->status->value === 'rejected' && $service->moderation_note)
                                    <x-marketplace.moderation-note :note="$service->moderation_note" />
                                @endif
                            </div>

                            <div class="flex flex-row md:flex-col items-end justify-between md:justify-center gap-3 shrink-0">
                                <x-ui.money :cents="$service->price_cents" class="text-lg font-extrabold text-stone-900" />

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('provider.services.edit', $service->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 shadow-sm transition-colors">
                                        Edit
                                    </a>

                                    @if(in_array($service->status->value, ['draft', 'rejected']))
                                        <form action="{{ route('provider.services.submit', $service->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                                                Submit Review
                                            </button>
                                        </form>
                                    @endif

                                    @if($service->status->value === 'active')
                                        <form action="{{ route('provider.services.archive', $service->id) }}" method="POST" onsubmit="return confirm('Archive this service? It will no longer appear in search.');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-stone-200 bg-stone-50 hover:bg-stone-100 text-stone-600 transition-colors">
                                                Archive
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 border-t border-stone-200">
                    {{ $services->links() }}
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
