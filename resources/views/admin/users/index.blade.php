<x-layouts.app title="User Management - Admin">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">User Accounts</h1>
                <p class="text-sm text-stone-500 mt-1">Directory of customers, providers, and administrators.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm mb-6">
            <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email..." class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <select name="role" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Roles</option>
                    <option value="customer" @selected(request('role') === 'customer')>Customer</option>
                    <option value="provider" @selected(request('role') === 'provider')>Provider</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                </select>

                <select name="status" class="px-3 py-2 bg-stone-50 border border-stone-200 rounded-lg text-xs text-stone-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">All Statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
                </select>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" variant="primary" class="w-full text-xs">
                        Filter
                    </x-ui.button>
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 text-xs text-stone-500 hover:text-stone-800">
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
                            <th class="px-5 py-3.5 text-left">User</th>
                            <th class="px-5 py-3.5 text-left">Email</th>
                            <th class="px-5 py-3.5 text-left">Role</th>
                            <th class="px-5 py-3.5 text-left">County</th>
                            <th class="px-5 py-3.5 text-left">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200 bg-white">
                        @foreach($users as $u)
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-stone-900 text-xs">{{ $u->name }}</div>
                                    <div class="text-[11px] text-stone-400">ID: {{ $u->id }}</div>
                                </td>
                                <td class="px-5 py-4 text-xs font-mono text-stone-600">{{ $u->email }}</td>
                                <td class="px-5 py-4">
                                    <span class="capitalize text-xs font-semibold px-2 py-0.5 rounded-full {{ $u->role->value === 'admin' ? 'bg-purple-100 text-purple-800' : ($u->role->value === 'provider' ? 'bg-blue-100 text-blue-800' : 'bg-stone-100 text-stone-800') }}">
                                        {{ $u->role->value }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-stone-600">
                                    {{ $u->county?->name ?? ($u->providerProfile?->county?->name ?? '—') }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $u->status->value === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($u->status->value) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.users.show', $u->id) }}" class="text-xs font-semibold text-stone-700 hover:text-stone-900">
                                        View
                                    </a>

                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('admin.users.status', $u->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Toggle user status?');">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $u->status->value === 'active' ? 'suspended' : 'active' }}">
                                            <button type="submit" class="text-xs font-semibold {{ $u->status->value === 'active' ? 'text-red-600 hover:text-red-800' : 'text-emerald-700 hover:text-emerald-900' }}">
                                                {{ $u->status->value === 'active' ? 'Suspend' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-200">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-layouts.app>
