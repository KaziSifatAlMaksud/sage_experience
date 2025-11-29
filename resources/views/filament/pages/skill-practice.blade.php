<x-filament::page>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // ...existing event handlers...
         

        window.addEventListener('scrollToTop', () => {
      
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>


    <div x-data x-init="$nextTick(() => $dispatch('scrollToTop'))">
    <!-- Select top three skills I demonstrated well -->
        <!-- Final Summary View (shown after all selections are made) -->
        @if($showSummary ?? false)
            <x-filament::section>
                <x-slot name="heading">
                    Complete Skills Selection
                </x-slot>
                <x-slot name="description">
                    Review your selected skills below
                </x-slot>

                <div class="space-y-6">
                    <!-- Demonstrated Skills -->
                    @if(!empty($currentStrengths))
                       
                        <h2 class="text-xl font-bold">Skills you demonstrated well most recently:</h2>
                       
                    
                        @for($i = 1; $i <= 3; $i++)
                            @if(!empty($currentStrengths[$i]['skill_id']))
                                @php
                                    $area = App\Models\SkillArea::find($currentStrengths[$i]['area_id']);
                                    $skill = App\Models\Skill::find($currentStrengths[$i]['skill_id']);
                                    $practice = App\Models\Practice::find($currentStrengths[$i]['practice_id']);
                                    $baseColor = $area->color ?? '#666';
                                    [$r, $g, $b] = sscanf($baseColor, "#%02x%02x%02x");
                                    $midColor = sprintf("#%02x%02x%02x", max($r-30, 0), max($g-30, 0), max($b-30, 0));
                                    $darkColor = sprintf("#%02x%02x%02x", max($r-60, 0), max($g-60, 0), max($b-60, 0));
                                    
                                @endphp

                                <div class="mb-4">
                                    <h3 class="text-lg font-medium mb-2">Skill {{ $currentSelection }}</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-3 py-1 text-white uppercase font-bold rounded-md" style="background-color: {{ $baseColor }}; margin-right: 8px;">
                                            {{ $area->name }}
                                        </span>
                                        <span class="px-3 py-1 text-white rounded-md" style="background-color: {{ $midColor }}">
                                            {{ $skill->name }}
                                        </span>
                                        <span class="px-3 py-1 text-white rounded-md" style="background-color: {{ $darkColor }}">
                                            {{ $practice->description }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endfor
     
                    @endif

                    <!-- Future Skills -->
                    @if(!empty($skillsToImprove))
                        <h2 class="text-xl font-bold mt-8">Skills you want to work on next:</h2>
                        @for($i = 1; $i <= 3; $i++)
                            @if(!empty($skillsToImprove[$i]['skill_id']))
                                @php
                                    $area = App\Models\SkillArea::find($skillsToImprove[$i]['area_id']);
                                    $skill = App\Models\Skill::find($skillsToImprove[$i]['skill_id']);
                                    $practice = App\Models\Practice::find($skillsToImprove[$i]['practice_id']);
                                    // $baseColor = $colors[$area->id] ?? '#666';
                                    $baseColor = $area->color ?? '#666';
                                    [$r, $g, $b] = sscanf($baseColor, "#%02x%02x%02x");
                                    $midColor = sprintf("#%02x%02x%02x", max($r-30, 0), max($g-30, 0), max($b-30, 0));
                                    $darkColor = sprintf("#%02x%02x%02x", max($r-60, 0), max($g-60, 0), max($b-60, 0));
                                @endphp

                                <div class="mb-4">
                                    <h3 class="text-lg font-medium mb-2">Skill {{ $currentSelection }}</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="px-3 py-1 text-white uppercase font-bold rounded-md" style="background-color: {{ $baseColor }}; margin-right: 8px;">
                                            {{ $area->name }}
                                        </span>
                                        <span class="px-3 py-1 text-white rounded-md" style="background-color: {{ $midColor }}">
                                            {{ $skill->name }}
                                        </span>
                                        <span class="px-3 py-1 text-white rounded-md" style="background-color: {{ $darkColor }}">
                                            {{ $practice->description }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endfor
                    @endif

                    <div class="flex justify-end space-x-2 mt-6">
                        <x-filament::button wire:click="resetAndStartOver" color="gray">
                            Back
                        </x-filament::button>
                        <x-filament::button wire:click="finalSave" >
                            Save
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @else


            <x-filament::section>
                <x-slot name="heading">
                    {{ $questions[$currentQuestion] }}
                </x-slot>
                <x-slot name="description">
                    Select top three skills I demonstrated well
                </x-slot>

                <!-- Progress Indicator -->
                 
                <div x-data="{ tab: @entangle('navTab').live }" class="mb-4">
                <div class="mb-6">
                    <div class="flex justify-between mb-2">
                        {{-- <div class="font-medium">{{ $currentStep }} of 3</div> --}}
                        <div class="text-black-500 font-bold">
                            <span x-text="tab === 'first' ? 'Select three skills' : 'Future Practice Skills'"></span> -
                            {{ $currentSelection }} of 3
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ ($currentSkillSet - 1) * 33.3 + $currentStep * 11.1 }}%"></div>
                    </div>
                </div>

                
                <div>
                    @forelse($savedSkills as $item)
                        <div class="flex gap-2 mb-2">
                            <span class="px-3 py-1 text-white font-bold rounded-md" style="background-color: {{ $item['color']['base'] }}">
                                {{ $item['area'] }}
                            </span>
                            <span class="px-3 py-1 text-white rounded-md" style="background-color: {{ $item['color']['mid'] }}">
                                {{ $item['skill'] }}
                            </span>
                            <span class="px-3 py-1 text-white rounded-md" style="background-color: {{ $item['color']['dark'] }}">
                                {{ $item['practice'] }}
                            </span>
                        </div>
                    @empty
                        <div class="text-gray-500 italic">
                            No skills selected yet.
                        </div>
                    @endforelse
                </div>

                


                <!-- Show current selections summary -->
                        <div id="selection{{ $currentSkillSet }}" class="mt-2 mb-6">
                        
                            <div class="flex gap-4 border-b mb-4">
                                
                                <!-- Past Performance -->
                                <button 
                                    @click="tab = 'first'" wire:click="resetAndStartOver"
                                    :class="tab === 'first' ? 'border-b-2 border-indigo-400' : ''" 
                                    class="flex items-center gap-2 px-2 py-1"
                                >
                                    <img :style="tab === 'first' ? 'display: none;' : ''" src="{{ asset('images/success-icon.png') }}" alt="Past Performance Icon" class="w-7 h-7">
                                    <span>Evaluate Recent Performance</span>
                                </button>

                                <!-- Select Skills -->
                                <button 
                                    @click="tab = 'second'" wire:click="resetAndStartOver"
                                    :class="tab === 'second' ? 'border-b-2 border-indigo-400' : ''"
                                    class="px-2 py-1"
                                    :disabled="!@js($enableFutureTab)"
                                    :style="!@js($enableFutureTab) ? 'opacity: 0.5;cursor: not-allowed;' : ''"
                                >
                                    Select Future Skills Practice
                                </button>
                                
                            </div>
                        </div>
                        <!-- 2 tab  -->

                    
                     Skill - {{ $currentSelection }} 


                     @if($currentQuestion === 1)

                    


                            @php
                                $currentSelection = $currentStrengths[$currentSkillSet] ?? [];

                                if (!empty($skillsToImprove)) {
                                    $currentSelection = $skillsToImprove[$currentSkillSet] ?? [];
                                }
                            @endphp
                           


                            @if(isset($currentSelection['area_id']) && $currentSelection['area_id'])
                                @php
                                    $area = App\Models\SkillArea::find($currentSelection['area_id']);
                                @endphp
                                <span id="area_selection_{{ $currentSkillSet }}" class="inline-block px-3 py-1 m-1 text-white rounded-md" style="background-color: {{$area->color ?? '#666' }}; margin-right: 8px;">
                                    {{ $area->name }}
                                </span>
                            @endif

                            @if(isset($currentSelection['skill_id']) && $currentSelection['skill_id'])
                                @php
                                    $skill = App\Models\Skill::find($currentSelection['skill_id']);
                                @endphp
                                <span id="skill_selection_{{ $currentSkillSet }}" class="inline-block px-3 py-1 m-1 text-white rounded-md" style="background-color: {{ $area->color ?? '#666' }}; margin-right: 8px;">
                                    {{ $skill->name }}
                                </span>
                            @endif

                            @if(isset($currentSelection['practice_id']) && $currentSelection['practice_id'])
                                @php
                                    $practice = App\Models\Practice::find($currentSelection['practice_id']);
                                @endphp
                                <span id="practice_selection_{{ $currentSkillSet }}" class="inline-block px-3 py-1 m-1 text-white rounded-md" style="background-color: {{ $area->color ?? '#666' }}; margin-right: 8px;">
                                    {{ $practice->description }}
                                </span>
                            @endif
                            
                        @else
                            @php
                                $currentSelection = $skillsToImprove[$currentSkillSet] ?? [];
                            @endphp
                         

                            @if(isset($currentSelection['area_id']) && $currentSelection['area_id'])
                                @php
                                    $area = App\Models\SkillArea::find($currentSelection['area_id']);
                                @endphp
                                <span id="area_selection_{{ $currentSkillSet }}" class="inline-block px-3 py-1 m-1 text-white rounded-md" style="background-color: {{ $area->color ?? '#666' }}; margin-right: 8px;">
                                    {{ $area->name }}
                                </span>
                            @endif

                            @if(isset($currentSelection['skill_id']) && $currentSelection['skill_id'])
                                @php
                                    $skill = App\Models\Skill::find($currentSelection['skill_id']);
                                @endphp
                                <span id="skill_selection_{{ $currentSkillSet }}" class="inline-block px-3 py-1 m-1 text-white rounded-md" style="background-color: {{ $area->color ?? '#666' }}; margin-right: 8px;">
                                    {{ $skill->name }}
                                </span>
                            @endif
                          
                            @if(isset($currentSelection['practice_id']) && $currentSelection['practice_id'])
                                @php
                                    $practice = App\Models\Practice::find($currentSelection['practice_id']);
                                @endphp
                                  
                                <span id="practice_selection_{{ $currentSkillSet }}" class="inline-block px-3 py-1 m-1 text-white rounded-md" style="background-color: {{ $area->color ?? '#666' }}; margin-right: 8px;">
                                    {{ $practice->description }}
                                </span>
                            @endif
                        @endif 
                    </div>

                   
                           

                    <!-- Step 1: Select Skill Area -->
                    @if($currentStep === 1)
                        <div id="div_area_{{ $currentSkillSet }}" class="space-y-4">
                            <label for="skillArea" class="block text-sm font-medium mb-2">
                                Select Skill Area:
                            </label>

                            <div
                            x-data @click="$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }))" 
                            class="grid grid-cols-1 gap-4">
                                @foreach($skillAreas as $area)
                                    <div wire:click.prevent="selectSkillArea({{ $area->id }})" class="cursor-pointer transition-all duration-200 skill-area-card">
                                        <div class="p-4 border rounded-lg {{ $selectedSkillAreaId == $area->id ? 'ring-2 ring-primary-500' : '' }}" style="border: 4px solid {{ $area->color ?? '#666' }}">
                                            <h3 class="font-medium text-lg">{{ $area->name }}</h3>
                                            <p class="text-sm text-gray-500">{{ $area->description }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif


                    <!-- Step 2: Select Skill -->
                    @if($currentStep === 2)
                        <div id="subgroup-div-{{ $currentSkillSet }}" class="space-y-4">
                            <div class="flex justify-between items-center mb-2">
                                <label for="skill" class="block text-sm font-medium">
                                    Select Specific Skill:
                                </label>
                                <button type="button" class="flex items-center text-sm text-primary-600 hover:text-primary-700" wire:click="goBackToSkillArea">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    <span>Back</span>
                                </button>
                            </div>


                           

                            <div  x-data 
                                @click="$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }))" class="grid grid-cols-1 gap-4">
                                @php
                                    logger()->debug('Skills in template display', [
                                        'count' => count($skills),
                                        'unique_count' => count(array_unique(array_column($skills, 'id')))
                                    ]);
                                @endphp

                            @foreach($skills as $skill)
                                    <div wire:click.prevent="selectSkill({{ $skill['id'] }})" class="cursor-pointer transition-all duration-200 skill-card">
                                        <div 
                                            class="p-4 border rounded-lg {{ $selectedSkillId == $skill['id'] ? 'ring-2 ring-primary-500' : '' }}" 
                                            style="border-left: 4px solid {{ $skill['color'] ?? '#666' }};"
                                        >
                                            <h3 class="font-medium text-lg">{{ $skill['name'] }}</h3>
                                            <p class="text-sm text-gray-500">{{ $skill['description'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Step 3: Select Practice -->
                    <div class="practice-wrapper">
                        @if($currentStep === 3)
                            <div id="skill-div-{{ $currentSkillSet }}" class="space-y-4">
                                <div class="flex justify-between items-center mb-2">
                                    <label for="practice" class="block text-sm font-medium">
                                        Select Skill Practice:
                                    </label>
                                    <button type="button" class="text-sm text-primary-600 flex items-center" wire:click="goBackToSkill">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                        <span>Back</span>
                                    </button>
                                </div>

                                <div  x-data 
                                            @click="$nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }))" class="grid grid-cols-1 gap-4">
                                    @foreach($practices as $practice)
                                        <div wire:click.prevent="selectPractice({{ $practice['id'] }})" class="cursor-pointer transition-all duration-200 practice-card">
                                            <div class="p-4 border rounded-lg {{ $selectedPracticeId == $practice['id'] ? 'ring-2 ring-primary-500' : '' }}">
                                                <p class="text-sm text-gray-500 practice-description">{{ $practice['description'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </x-filament::section>

                <!-- Previous Selections Section -->



            @if($currentQuestion === 1 && count($userSkillPractices) > 0)
                    <x-filament::section class="mt-8">
                
                        <h2 class="text-xl font-semibold mb-4">Recent Skill Selection</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @php
                                $groupedPractices = $userSkillPractices->take(3)->groupBy(function($item) {
                                    return $item->skill_id . '-' . $item->practice->name;
                                })->map(function($group) {
                                    return $group->first();
                                })->values();
                            @endphp
                            @foreach($groupedPractices as $practice)
                                <div class="border rounded-lg p-4 border-l-4" style="border-left-color: {{ $practice->skillArea->color ?? '#666' }}">
                                    <h3 class="font-medium text-lg">{{ $practice->skill->name }}</h3>
                                    <div class="text-sm text-gray-500">{{ $practice->skillArea->name }}</div>
                                    <div class="text-sm text-gray-500 mt-1">{{ $practice->practice->description }}</div>
                                    <div class="text-xs text-gray-400 mt-1">Selected on {{ $practice->selected_at ? $practice->selected_at->format('M d, Y') : 'Unknown date' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </x-filament::section>
                @endif

              <!-- Previous Selections Section End-->








            @if($currentQuestion === 2 && count($userFuturePractices) > 0)
                <x-filament::section>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $groupedFuturePractices = $userFuturePractices->take(3)->groupBy(function($item) {
                                return $item->skill_id . '-' . $item->practice->name;
                            })->map(function($group) {
                                return $group->first();
                            })->values();
                        @endphp
                        @foreach($groupedFuturePractices as $practice)
                            <div class="border rounded-lg p-4 border-l-4" style="border-left-color: {{ $colors[$practice->skillArea->id] ?? '#666' }}">
                                <h3 class="font-medium text-lg">{{ $practice->skill->name }}</h3>
                                <div class="text-sm text-gray-500">{{ $practice->skillArea->name }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ $practice->practice->description }}</div>
                                <div class="text-xs text-gray-400 mt-1">Selected on {{ $practice->selected_at ? $practice->selected_at->format('M d, Y') : 'Unknown date' }}</div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @endif

            <!-- Skip buttons -->
            @if($currentStep === 3)
                <div class="mt-6 flex justify-end">
                    <x-filament::button wire:click="skipRemainingSkills" class="mr-2">
                        @if($currentQuestion === 1)
                            Continue to Next Question
                        @else
                            Review Selections
                        @endif
                    </x-filament::button>
                </div>
            @endif
        @endif

        <!-- JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const attachHandlers = () => {
                    document.querySelectorAll('.skill-area-card, .skill-card, .practice-card').forEach(card => {
                        card.addEventListener('click', function(e) {
                            const wireClick = this.getAttribute('wire:click');
                            if (wireClick && window.Livewire) {
                                const componentId = this.closest('[wire\\:id]')?.getAttribute('wire:id');
                                if (componentId) {
                                    try {
                                        const match = wireClick.match(/^([^(]+)(?:\(([^)]*)\))?$/);
                                        if (match) {
                                            const method = match[1];
                                            const params = match[2] ? match[2].split(',').map(p => p.trim()) : [];
                                            window.Livewire.find(componentId)[method](...params);
                                        }
                                    } catch (e) {
                                        console.error('Error triggering Livewire event:', e);
                                    }
                                }
                            }
                        });
                    });
                };

                attachHandlers();

                window.addEventListener('skillAreaSelected', () => setTimeout(attachHandlers, 100));
                window.addEventListener('skillSelected', () => setTimeout(attachHandlers, 100));
                window.addEventListener('practiceSelected', () => setTimeout(attachHandlers, 100));
                window.addEventListener('nextSkillSet', () => setTimeout(attachHandlers, 100));
                window.addEventListener('nextQuestion', () => setTimeout(attachHandlers, 100));
                window.addEventListener('selectionsSaved', () => setTimeout(attachHandlers, 100));
            });
        </script>

        <!-- Styles -->
        <style>
            .skill-area-container {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                margin-bottom: 1.5rem;
            }

            .skill-area-item {
                cursor: pointer;
                border-radius: 0.375rem;
                padding: 0.5rem 1rem;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                background-color: white;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                border: 1px solid #e5e7eb;
            }

            .skill-area-item:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }

            .skill-area-item.selected {
                box-shadow: 0 0 0 2px currentColor;
            }

            .skill-area-color {
                width: 1rem;
                height: 1rem;
                border-radius: 9999px;
                margin-right: 8px;
            }

            .skill-area-name {
                font-weight: 600;
                font-size: 0.875rem;
            }

            .skills-list {
                margin-top: 1rem;
            }

            .skill-item {
                padding: 0.75rem;
                border-radius: 0.375rem;
                margin-bottom: 0.5rem;
                cursor: pointer;
                transition: all 0.3s ease;
                background-color: white;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                border: 1px solid #e5e7eb;
            }

            .skill-item:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }

            .skill-item.selected {
                box-shadow: 0 0 0 2px currentColor;
            }

            .practice-item {
                padding: 0.75rem;
                border-radius: 0.375rem;
                margin-bottom: 0.5rem;
                cursor: pointer;
                transition: all 0.3s ease;
                background-color: white;
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                border: 1px solid #e5e7eb;
                position: relative;
            }

            .practice-item:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }

            .practice-item.selected {
                box-shadow: 0 0 0 2px currentColor;
            }

            .practice-title {
                font-weight: 600;
                margin-bottom: 0.25rem;
            }

            .practice-description {
                font-size: 0.875rem;
                color: #6b7280;
            }

            .recent-skills-title {
                font-size: 1.125rem;
                font-weight: 600;
                margin-top: 2rem;
                margin-bottom: 1rem;
                padding-top: 1rem;
                border-top: 1px solid #e5e7eb;
            }

            .recent-skill-item {
                background-color: white;
                padding: 1rem;
                border-radius: 0.375rem;
                margin-bottom: 0.75rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
                border-left: 4px solid;
            }

            .recent-skill-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 0.5rem;
            }

            .recent-skill-name {
                font-weight: 600;
            }

            .recent-skill-date {
                font-size: 0.75rem;
                color: #6b7280;
            }

            .recent-skill-practice {
                font-size: 0.875rem;
            }

            .question-header {
                font-size: 1.25rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
                color: #1a202c;
            }

            .back-button {
                background-color: transparent;
                border: none;
                color: #6b7280;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                font-size: 0.875rem;
                margin-bottom: 1rem;
            }

            .back-button svg {
                width: 1rem;
                height: 1rem;
                margin-right: 0.25rem;
            }

            .complete-message {
                background-color: #f0fff4;
                border: 1px solid #c6f6d5;
                color: #22543d;
                padding: 1rem;
                border-radius: 0.375rem;
                margin-top: 1rem;
                text-align: center;
            }

            .complete-message h3 {
                font-weight: 600;
                margin-bottom: 0.5rem;
            }

            .complete-message p {
                margin-bottom: 1rem;
            }

            .skill-set-number {
                display: inline-block;
                width: 1.5rem;
                height: 1.5rem;
                line-height: 1.5rem;
                text-align: center;
                background-color: #e5e7eb;
                color: #374151;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 600;
                margin-right: 0.5rem;
            }

            .skill-set-number.complete {
                background-color: #84e1bc;
                color: #065f46;
            }

            .skill-set-number.current {
                background-color: #3b82f6;
                color: white;
            }
        </style>
    </div>
</x-filament::page>