{{-- resources/views/filament/widgets/active-students-widget.blade.php --}}

@php
    use App\Filament\Resources\UserResource;
@endphp


<div x-data="{ showStudents: true }" class="p-4 bg-white shadow rounded-xl">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Active Students</h2>
        <button 
            @click="showStudents = !showStudents" 
            class="flex items-center gap-1 px-3 py-1.5 text-sm font-medium  bg-blue-600 rounded-md hover:bg-blue-700 transition"
        >
            <svg x-show="!showStudents" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="black">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg x-show="showStudents" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="black" viewBox="0 0 24 24" stroke="black">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span x-text="showStudents ? 'Hide' : 'Show'"></span>
        </button>
    </div>

    <div x-show="showStudents" x-transition>
        <ul class="space-y-3">
            @forelse ($students as $student)
              <a 
                        href="{{ UserResource::getUrl('edit', ['record' => $student['id']]) }}" 
                        class="text-sm font-semibold text-blue-600 hover:underline"
                    >
                <li class="p-3 border border-gray-200 rounded-lg shadow-sm bg-gray-50 hover:bg-white transition">
                    <div class="text-sm font-semibold text-gray-800">{{ $student['name'] }}</div>
                    @if (!empty($student['email']))
                        <div class="text-xs text-gray-600">Email: {{ $student['email'] }}</div>
                    @endif
                    @if (!empty($student['school']))
                        <div class="text-xs text-gray-600">School: {{ $student['school'] }}</div>
                    @endif
                </li>
                </a>
            @empty
                <li class="text-gray-500 italic">No Students Found.</li>
            @endforelse
        </ul>
    </div>
</div>
