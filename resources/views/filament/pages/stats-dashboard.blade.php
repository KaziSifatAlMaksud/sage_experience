<x-filament::page>
    <x-slot name="headerActions">
        @foreach ($this->actions as $action)
            {{ $action }}
        @endforeach
    </x-slot>

    <h1>{{ $this->getTitle() }}</h1>

    {{-- Example to include widgets --}}
    <!-- @livewire('current-strengths-widget')
    @livewire('skills-to-practice-widget') -->
</x-filament::page>
