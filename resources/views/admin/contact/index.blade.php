<x-layouts.app title="Contact Messages - Admin">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Support Inquiries</h1>
                <p class="text-sm text-stone-500 mt-1">Review inquiries received through the public help centre.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm mb-6">
            <form action="{{ route('admin.contact.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search name, email, subject..." class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <select name="status" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Statuses</option>
                    <option value="new" @selected(($filters['status'] ?? '') === 'new')>Unread (New)</option>
                    <option value="read" @selected(($filters['status'] ?? '') === 'read')>Read</option>
                </select>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" variant="primary" class="w-full text-xs">
                        Filter
                    </x-ui.button>
                    <a href="{{ route('admin.contact.index') }}" class="px-3 py-2 text-xs text-stone-500 hover:text-stone-800">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Messages List --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
            @if($messages->isEmpty())
                <div class="p-8 text-center text-xs text-stone-500">
                    No contact messages found.
                </div>
            @else
                <div class="divide-y divide-stone-100">
                    @foreach($messages as $msg)
                        <div class="p-5 flex flex-col md:flex-row md:items-start justify-between gap-4 {{ $msg->status->value === 'new' ? 'bg-amber-50/30' : '' }}">
                            <div class="space-y-1.5 max-w-2xl">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $msg->status->value === 'new' ? 'bg-amber-100 text-amber-800' : 'bg-stone-100 text-stone-600' }}">
                                        {{ ucfirst($msg->status->value) }}
                                    </span>
                                    <span class="text-xs text-stone-400 font-mono">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <h3 class="text-sm font-bold text-stone-900">{{ $msg->subject }}</h3>
                                <p class="text-xs text-stone-500">
                                    From: <strong>{{ $msg->name }}</strong> (<a href="mailto:{{ $msg->email }}" class="text-emerald-700 hover:underline">{{ $msg->email }}</a>)
                                </p>
                                <p class="text-xs text-stone-700 bg-stone-50 p-3 rounded-lg border border-stone-200 mt-2 whitespace-pre-line leading-relaxed">
                                    {{ $msg->message }}
                                </p>
                            </div>

                            <div class="shrink-0 pt-2">
                                @if($msg->status->value === 'new')
                                    <form action="{{ route('admin.contact.read', $msg->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                                            Mark as Read
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-stone-400">Read {{ $msg->read_at ? $msg->read_at->format('d/m/Y') : '' }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 border-t border-stone-200">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
