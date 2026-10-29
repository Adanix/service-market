<x-layouts.app title="500 Server Error - ServiceLocal">
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-6">
            <x-ui.icon name="alert-triangle" class="w-8 h-8" />
        </div>
        <span class="text-xs font-bold text-red-700 uppercase tracking-widest">Error 500</span>
        <h1 class="text-3xl font-extrabold text-stone-900 mt-2 mb-4">Internal Server Error</h1>
        <p class="text-sm text-stone-600 mb-8 max-w-md mx-auto leading-relaxed">
            An unexpected error occurred while processing your request. Our system has logged the event for investigation.
        </p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="px-5 py-2.5 bg-stone-900 text-white font-semibold text-xs rounded-xl hover:bg-stone-800 transition-colors">
                Return Home
            </a>
            <a href="{{ route('help') }}" class="px-5 py-2.5 bg-stone-100 text-stone-800 font-semibold text-xs rounded-xl hover:bg-stone-200 transition-colors">
                Contact Support
            </a>
        </div>
    </div>
</x-layouts.app>
