<x-layouts.app title="404 Not Found - ServiceLocal">
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-stone-100 text-stone-500 flex items-center justify-center mb-6">
            <x-ui.icon name="file-question" class="w-8 h-8" />
        </div>
        <span class="text-xs font-bold text-stone-500 uppercase tracking-widest">Error 404</span>
        <h1 class="text-3xl font-extrabold text-stone-900 mt-2 mb-4">Page Not Found</h1>
        <p class="text-sm text-stone-600 mb-8 max-w-md mx-auto leading-relaxed">
            The service listing, provider profile, or page you were looking for could not be found or may have been archived.
        </p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="px-5 py-2.5 bg-stone-900 text-white font-semibold text-xs rounded-xl hover:bg-stone-800 transition-colors">
                Return Home
            </a>
            <a href="{{ route('services.index') }}" class="px-5 py-2.5 bg-emerald-600 text-white font-semibold text-xs rounded-xl hover:bg-emerald-700 transition-colors">
                Browse Services
            </a>
        </div>
    </div>
</x-layouts.app>
