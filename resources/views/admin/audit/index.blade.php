<x-layouts.app title="Audit Log - Admin">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Audit Trail</h1>
                <p class="text-sm text-stone-500 mt-1">Immutable security ledger of all platform actions, state transitions, and admin decisions.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm mb-6">
            <form action="{{ route('admin.audit.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search action or meta..." class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <select name="action" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}" @selected(($filters['action'] ?? '') === $act)>{{ $act }}</option>
                    @endforeach
                </select>

                <select name="actor_id" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Actors</option>
                    @foreach($actors as $actor)
                        <option value="{{ $actor->id }}" @selected(($filters['actor_id'] ?? '') == $actor->id)>{{ $actor->name }} ({{ $actor->role->value }})</option>
                    @endforeach
                </select>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" variant="primary" class="w-full text-xs">
                        Filter
                    </x-ui.button>
                    <a href="{{ route('admin.audit.index') }}" class="px-3 py-2 text-xs text-stone-500 hover:text-stone-800">
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
                            <th class="px-5 py-3.5 text-left">Timestamp</th>
                            <th class="px-5 py-3.5 text-left">Actor</th>
                            <th class="px-5 py-3.5 text-left">Action</th>
                            <th class="px-5 py-3.5 text-left">Auditable Entity</th>
                            <th class="px-5 py-3.5 text-left">Sanitized Metadata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @foreach($logs as $log)
                            <tr class="hover:bg-stone-50/70 transition-colors font-mono text-xs">
                                <td class="px-5 py-3.5 text-stone-500 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-5 py-3.5 font-sans font-bold text-stone-800">
                                    {{ $log->actor?->name ?? 'System' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="bg-stone-100 text-stone-800 px-2 py-0.5 rounded text-xs">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-stone-600 font-sans text-xs">
                                    @if($log->auditable_type)
                                        {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-stone-500 truncate max-w-sm text-[11px]">
                                    {{ json_encode($log->meta) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-200">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
