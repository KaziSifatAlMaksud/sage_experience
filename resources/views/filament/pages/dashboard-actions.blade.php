<x-filament::page>
    {{-- Optional: Admin-only widgets (if any) --}}
    @if (!auth()->user()->hasRole('student'))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            {{-- Example widgets --}}
            {{-- <livewire:current-strengths-widget /> --}}
            {{-- <livewire:skills-to-practice-widget /> --}}
        </div>
    @endif

    {{-- Student-only Actions --}}
    @if (auth()->user()->hasRole('student') && count($actions))
        <div class="mt-8 flex flex-col md:flex-row flex-wrap gap-4">
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</x-filament::page>
