<x-layouts.app title="403 Forbidden - ServiceLocal">
    <div class="max-w-2xl mx-auto px-4 py-16 text-center">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-6">
            <x-ui.icon name="shield-alert" class="w-8 h-8" />
        </div>
        <span class="text-xs font-bold text-amber-700 uppercase tracking-widest">Error 403</span>
        <h1 class="text-3xl font-extrabold text-stone-900 mt-2 mb-4">Access Restricted</h1>
        <p class="text-sm text-stone-600 mb-8 max-w-md mx-auto leading-relaxed">
            {{ $exception->getMessage() ?: 'You do not have permission to access this page or resource with your current account role.' }}
        </p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="px-5 py-2.5 bg-stone-900 text-white font-semibold text-xs rounded-xl hover:bg-stone-800 transition-colors">
                Return Home
            </a>
            <a href="{{ route('demo.sign-in') }}" class="px-5 py-2.5 bg-emerald-600 text-white font-semibold text-xs rounded-xl hover:bg-emerald-700 transition-colors">
                Switch Demo Account
            </a>
        </div>
    </div>
</x-layouts.app>
