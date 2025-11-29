<x-filament-panels::page>
    <x-filament::section>
        <div class="mb-6">
            <h2 class="text-xl font-bold">{{ $record->name }}'s Selected Skill Practices</h2>
            <p class="text-gray-500">View practices this student selected and any associated feedback</p>
        </div>

        <div class="grid grid-cols-1 gap-y-8">
            @if($demonstratedPractices->count() > 0)
                <x-filament::section>
                    <x-slot name="heading">Demonstrated Skills</x-slot>
                    <x-slot name="description">Practices the student has demonstrated well recently</x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($demonstratedPractices as $practice)
                            <div class="border rounded-lg p-4 border-l-4" style="border-left-color: {{ $practice->skillArea ? '#' . substr(md5($practice->skillArea->name), 0, 6) : '#666' }}">
                                <h3 class="font-medium text-lg">{{ $practice->skill->name }}</h3>
                                <div class="text-sm text-gray-500">{{ $practice->skillArea->name }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ $practice->practice->description }}</div>
                                <div class="text-xs text-gray-400 mt-1">Selected on {{ $practice->selected_at ? $practice->selected_at->format('M d, Y') : 'Unknown date' }}</div>

                                @if(isset($feedbackByPractice[$practice->practice_id]) && count($feedbackByPractice[$practice->practice_id]) > 0)
                                    <div class="mt-3 pt-2 border-t">
                                        <span class="text-sm font-medium">Feedback ({{ count($feedbackByPractice[$practice->practice_id]) }})</span>
                                        @foreach($feedbackByPractice[$practice->practice_id] as $feedback)
                                            <div class="mt-2 p-2 rounded-md {{ $feedback->is_positive ? 'bg-green-50' : 'bg-amber-50' }}">
                                                <p class="text-sm">{{ Str::limit($feedback->comments, 100) }}</p>
                                                <div class="flex justify-between items-center mt-1">
                                                    <span class="text-xs text-gray-500">From: {{ $feedback->sender->name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $feedback->created_at->format('M d, Y') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            @if($futurePractices->count() > 0)
                <x-filament::section>
                    <x-slot name="heading">Skills to Work On</x-slot>
                    <x-slot name="description">Practices the student wants to improve</x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($futurePractices as $practice)
                            <div class="border rounded-lg p-4 border-l-4" style="border-left-color: {{ $practice->skillArea ? '#' . substr(md5($practice->skillArea->name), 0, 6) : '#666' }}">
                                <h3 class="font-medium text-lg">{{ $practice->skill->name }}</h3>
                                <div class="text-sm text-gray-500">{{ $practice->skillArea->name }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ $practice->practice->description }}</div>
                                <div class="text-xs text-gray-400 mt-1">Selected on {{ $practice->selected_at ? $practice->selected_at->format('M d, Y') : 'Unknown date' }}</div>

                                @if(isset($feedbackByPractice[$practice->practice_id]) && count($feedbackByPractice[$practice->practice_id]) > 0)
                                    <div class="mt-3 pt-2 border-t">
                                        <span class="text-sm font-medium">Feedback ({{ count($feedbackByPractice[$practice->practice_id]) }})</span>
                                        @foreach($feedbackByPractice[$practice->practice_id] as $feedback)
                                            <div class="mt-2 p-2 rounded-md {{ $feedback->is_positive ? 'bg-green-50' : 'bg-amber-50' }}">
                                                <p class="text-sm">{{ Str::limit($feedback->comments, 100) }}</p>
                                                <div class="flex justify-between items-center mt-1">
                                                    <span class="text-xs text-gray-500">From: {{ $feedback->sender->name }}</span>
                                                    <span class="text-xs text-gray-500">{{ $feedback->created_at->format('M d, Y') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            @if($demonstratedPractices->count() == 0 && $futurePractices->count() == 0)
                <div class="text-center py-8">
                    <div class="text-gray-400 mb-2">
                        <x-heroicon-o-academic-cap class="inline-block w-12 h-12" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No skill practices selected yet</h3>
                    <p class="mt-1 text-sm text-gray-500">This student hasn't selected any skill practices to work on.</p>
                </div>
            @endif
        </div>
    </x-filament::section>

    <x-filament::section>
        <div class="flex justify-between items-center">
            <x-filament::button
                color="gray"
                tag="a"
                :href="route('filament.admin.resources.users.index')"
            >
                Back to Users
            </x-filament::button>

            <x-filament::button
                color="primary"
                tag="a"
                :href="route('filament.admin.resources.feedback.create', ['receiver_id' => $record->id])"
            >
                Give Feedback
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-panels::page>
