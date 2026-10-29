<x-layouts.app title="Notifications - ServiceLocal">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Notifications</h1>
                <p class="text-sm text-stone-500 mt-1">Updates regarding your bookings, payments, and account status.</p>
            </div>

            @if(auth()->user()->unreadNotifications->isNotEmpty())
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        {{-- Filters --}}
        <div class="border-b border-stone-200 mb-6">
            <nav class="flex space-x-6 text-sm font-medium">
                <a href="{{ route('notifications.index') }}" class="pb-3 border-b-2 {{ $filter === 'all' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    All Notifications
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="pb-3 border-b-2 {{ $filter === 'unread' ? 'border-emerald-600 text-emerald-700 font-bold' : 'border-transparent text-stone-500 hover:text-stone-700' }}">
                    Unread Only ({{ auth()->user()->unreadNotifications->count() }})
                </a>
            </nav>
        </div>

        {{-- Notifications List --}}
        @if($notifications->isEmpty())
            <x-ui.empty-state
                title="No notifications"
                description="You're all caught up! No notifications in this view."
                icon="bell"
            />
        @else
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden divide-y divide-stone-100">
                @foreach($notifications as $notification)
                    <div class="p-5 flex items-start justify-between gap-4 {{ $notification->read_at ? 'bg-white' : 'bg-emerald-50/30' }} hover:bg-stone-50/60 transition-colors">
                        <div class="space-y-1 max-w-2xl">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-stone-900 text-sm">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </span>
                                @if(!$notification->read_at)
                                    <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                @endif
                                <span class="text-xs text-stone-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <p class="text-xs text-stone-600 leading-relaxed">
                                {{ $notification->data['body'] ?? '' }}
                            </p>
                        </div>

                        <div class="shrink-0 pt-1">
                            @if(!empty($notification->data['url']))
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 shadow-sm transition-colors">
                                        View
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-stone-200 mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
