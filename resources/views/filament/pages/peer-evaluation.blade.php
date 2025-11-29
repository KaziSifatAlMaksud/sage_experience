
<x-filament-panels::page class="bg-[#E7F0E6] text-gray-800 min-h-screen">
  <x-filament::section class="border-gray-200">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800">Evaluate Team Member Performance</h2>
            <p class="text-gray-500">Select a team member and provide feedback on their skill practices</p>
        </div>

        {{-- <div class="mb-4">
            <h4 class="text-xl font-semibold text-gray-800">Your Teams:</h4>
            @if($userTeams->isEmpty())
                <p class="text-sm text-gray-600">You are not part of any team.</p>
            @else
                <ul class="list-disc list-inside text-sm text-gray-600">
                    @foreach($userTeams as $team)
                        <li class=''>{{ $team->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div> --}}

        @if($currentStep == 1)
            <!-- Step 1: Select Team Member -->
            <div class="mb-6">
                {{-- <h3 class="text-lg font-medium mb-4 text-gray-800">Select Team Member to Evaluate</h3> --}}

                @if($teamMembers->isEmpty())
                    <div class="bg-white rounded-lg p-6 shadow border border-gray-200 text-center">
                        <div class="text-gray-400 mb-2">
                            <x-heroicon-o-users class="inline-block w-12 h-12" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-800">No team members available</h3>
                        <p class="mt-1 text-sm text-gray-500">You need to be part of a team with other students to provide peer feedback.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($teamMembers as $member)
                            <div
                                wire:click="selectTeamMember({{ $member->id }})"
                                class="bg-white rounded-lg p-4 shadow border border-gray-200 cursor-pointer hover:bg-gray-50"
                            >
                                <div class="flex items-center">
                                    <div class="rounded-full bg-sage-600 p-2.5 mr-5 text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        </div>
                                    <div>
                                        <p class="text-lg font-medium text-gray-800">{{ $member->name }}</p>
                                        
                                        <!-- <p class="text-sm text-gray-500">
                                            @php
                                                $teams = $member->teams->pluck('name')->join(', ', ' and ');
                                            @endphp
                                            {{ $teams }}
                                        </p> -->
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
        @elseif($currentStep == 2)
            <!-- Step 2: Select Skills -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <button wire:click="goBack" class="flex items-center text-sage-600 hover:text-sage-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back
                    </button>
                   
                </div>

                <div class="bg-white rounded-lg p-6 shadow border border-gray-200 mb-6">
                    <h3 class="text-lg font-medium mb-2 text-gray-800">Evaluating: {{ $selectedMember->name }}</h3>
                    <p class="text-gray-500 mb-4">{{ $questions[$currentQuestion] }}</p>

                    @if(is_null($selectedSkillAreaId))
                        <!-- Skill Area Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                             <div class="text-sm text-gray-500">
                        {{ $currentSkillSet }} of {{ $maxSelections }}
            </div>
                    @foreach($skillAreas as $area)
                                <div
                                    wire:click="selectSkillArea({{ $area->id }})"
                                    class="p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                                    style="border-width: 4px; border-color: {{ $area->color ?? '#888' }};"
                                >
                                    <h4 class="font-medium text-gray-800">{{ $area->name }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $area->skills->count() }} skills</p>
                                </div>
                            @endforeach
                        </div>
                    @elseif(is_null($selectedSkillId))
                        <!-- Skill Selection -->
                        <div>
                            <div class="mb-4 p-3 bg-gray-100 rounded-lg">
                                <h4 class="font-medium text-gray-700">Selected Area: {{ $selectedSkillArea->name }}</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($skills as $skill)
                                    <div
                                        wire:click="selectSkill({{ $skill['id'] }})"
                                        class="p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                                        style="border-width: 4px; border-color: {{ $selectedSkillArea->color ?? '#888' }};"
                                    >
                                        <h4 class="font-medium text-gray-800">{{ $skill['name'] }}</h4>
                                        <p class="text-xs text-gray-500 mt-1">{{ $skill['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
                    @elseif(is_null($selectedPracticeId))
                        <!-- Practice Selection -->
                        <div>
                            <div class="mb-4 p-3 bg-gray-100 rounded-lg">
                                <h4 class="font-medium text-gray-700">Selected Skill: {{ $selectedSkill->name }}</h4>
                </div>

                            <div class="space-y-3">
                                @foreach($practices as $practice)
                                    <div
                                        wire:click="selectPractice({{ $practice->id }})"
                                        class="p-4 border rounded-lg cursor-pointer hover:bg-gray-50"
                                        style="border-width: 4px; border-color: {{ $selectedSkillArea->color ?? '#888' }};"
                                    >
                                        <p class="text-gray-800">{{ $practice->description }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @elseif($currentStep == 3)
            <!-- Step 3: Add Comments -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <button wire:click="goBack" class="flex items-center text-sage-600 hover:text-sage-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back
                    </button>
                </div>

                <div class="bg-white rounded-lg p-6 shadow border border-gray-200 mb-6">
                    <h3 class="text-lg font-medium mb-4 text-gray-800">Add Additional Comments</h3>

                    <div class="mb-6">
                        <h4 class="font-medium mb-2 text-gray-700">Selected Strengths:</h4>
                        <div class="space-y-2">
                            @foreach($currentStrengths as $index => $strength)
                                @if(isset($strength['practice_id']))
                                    @php
                                        $skill = App\Models\Skill::find($strength['skill_id']);
                                        $area = App\Models\SkillArea::find($strength['area_id']);
                                        $practice = App\Models\Practice::find($strength['practice_id']);
                                    @endphp
                                    <div class="p-3 border rounded-lg" style="border-left-width: 4px; border-left-color: {{ $area->color ?? '#888' }};">
                                        <p class="font-medium text-gray-800">{{ $area->name }}: {{ $skill->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $practice->description }}</p>
                                    </div>
                                @endif
                            @endforeach
                                </div>
                            </div>

                    <div class="mb-6">
                        <h4 class="font-medium mb-2 text-gray-700">Areas for Improvement:</h4>
                        <div class="space-y-2">
                            @foreach($skillsToImprove as $index => $improvement)
                                @if(isset($improvement['practice_id']))
                                    @php
                                        $skill = App\Models\Skill::find($improvement['skill_id']);
                                        $area = App\Models\SkillArea::find($improvement['area_id']);
                                        $practice = App\Models\Practice::find($improvement['practice_id']);
                                    @endphp
                                    <div class="p-3 border rounded-lg" style="border-left-width: 4px; border-left-color: {{ $area->color ?? '#888' }};">
                                        <p class="font-medium text-gray-800">{{ $area->name }}: {{ $skill->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $practice->description }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="comments" class="block text-sm font-medium text-gray-700 mb-1">Additional Comments</label>
                        <textarea
                            id="comments"
                            wire:model="feedbackComments"
                            rows="4"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-sage-500 focus:ring focus:ring-sage-500 focus:ring-opacity-50"
                            placeholder="Add any additional comments about your team member's performance..."
                        ></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button
                            wire:click="addFeedbackComment($('#comments').val())"
                            class="px-4 py-2 bg-sage-600 text-white rounded-md hover:bg-sage-700"
                        >
                            Continue to Review
                        </button>
                    </div>
                </div>
            </div>
        @elseif($currentStep == 4)
            <!-- Step 4: Review and Submit -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <button wire:click="goBack" class="flex items-center text-sage-600 hover:text-sage-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back
                    </button>
                </div>

                <div class="bg-white rounded-lg p-6 shadow border border-gray-200 mb-6">
                    <h3 class="text-lg font-medium mb-4 text-gray-800">Review Your Feedback</h3>

                    <div class="p-4 bg-gray-100 rounded-lg mb-6">
                        <h4 class="font-medium text-gray-800">For: {{ $selectedMember->name }}</h4>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-medium mb-2 text-gray-700">Positive Feedback:</h4>
                        <div class="space-y-2">
                            @foreach($currentStrengths as $index => $strength)
                                @if(isset($strength['practice_id']))
                                    @php
                                        $skill = App\Models\Skill::find($strength['skill_id']);
                                        $area = App\Models\SkillArea::find($strength['area_id']);
                                        $practice = App\Models\Practice::find($strength['practice_id']);
                                    @endphp
                                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                        <p class="font-medium text-gray-800">{{ $area->name }}: {{ $skill->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $practice->description }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-medium mb-2 text-gray-700">Areas for Improvement:</h4>
                        <div class="space-y-2">
                            @foreach($skillsToImprove as $index => $improvement)
                                @if(isset($improvement['practice_id']))
                                    @php
                                        $skill = App\Models\Skill::find($improvement['skill_id']);
                                        $area = App\Models\SkillArea::find($improvement['area_id']);
                                        $practice = App\Models\Practice::find($improvement['practice_id']);
                                    @endphp
                                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                        <p class="font-medium text-gray-800">{{ $area->name }}: {{ $skill->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $practice->description }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-medium mb-2 text-gray-700">Additional Comments:</h4>
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            @if(empty($feedbackComments))
                                <p class="text-gray-500 italic">No additional comments provided.</p>
                            @else
                                <p class="text-gray-800">{{ $feedbackComments }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            wire:click="submitFeedback"
                            class="px-5 py-2 bg-sage-600 text-white rounded-md hover:bg-sage-700"
                        >
                            Submit Feedback
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
