<x-filament::page>
    <div class="flex flex-col w-full h-[100vh] gap-y-4">
        

        <x-filament::button tag="a" href="{{ route('filament.admin.resources.teams.create') }}">
            Setup Project
        </x-filament::button>

        {{-- Show current projects as a widget --}}
       
       

        <x-filament::button color="primary" tag="a" href="{{ route('filament.admin.resources.teams.completed') }}">
            Past Projects  
        </x-filament::button>

          <div>
            @livewire(\App\Filament\Widgets\CurrentStudentsWidget::class)
        </div>

         <div>
            @livewire(\App\Filament\Widgets\CurrentProjectsWidget::class)
        </div>


    </div>
</x-filament::page>
