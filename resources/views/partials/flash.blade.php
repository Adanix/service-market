@if (session('success'))
    <div class="mb-6">
        <x-ui.alert variant="success">
            {{ session('success') }}
        </x-ui.alert>
    </div>
@endif

@if (session('error'))
    <div class="mb-6">
        <x-ui.alert variant="error">
            {{ session('error') }}
        </x-ui.alert>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6">
        <x-ui.alert variant="error" title="There were errors with your submission">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    </div>
@endif
