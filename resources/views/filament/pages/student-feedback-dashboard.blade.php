<x-filament-panels::page>
    
    <x-filament::section class="bg-[#f8faf7] border-gray-200">
        <div class="mb-8 flex flex-col gap-4">
            <div class="bg-white rounded-lg p-6 shadow border border-gray-200">
                <h3 class="text-xl font-medium mb-4 text-sage-700">Evaluate My Latest Performance</h3>
                <div class="flex flex-col md:flex-row gap-4">
                    <a href="{{ route('filament.admin.pages.skill-practice') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent rounded-md text-sm font-medium text-white bg-sage-600 hover:bg-sage-700 transition">
                        <x-heroicon-o-academic-cap class="h-5 w-5 mr-2" />
                        Evaluate My Latest Performance
                    </a>
                    <a href="{{ route('filament.admin.pages.peer-evaluation') }}" class="inline-flex items-center justify-center px-5 py-3 border border-gray-300 bg-white rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        <x-heroicon-o-user-group class="h-5 w-5 mr-2" />
                        Evaluate Team Member Performance
                    </a>
                </div>
            </div>
        </div>

       
        <div x-data="{ tab: 'first' }" class="mb-4">
            <div class="flex gap-4 border-b mb-4">
                
                <!-- Past Performance -->
                <button 
                    @click="tab = 'first'" 
                    :class="tab === 'first' ? 'border-b-2 border-indigo-400' : ''"
                    class="flex items-center gap-2 px-2 py-1"
                >
                    <span>My Recent Performance</span>
                </button>

                <!-- Select Skills -->
                <button 
                    @click="tab = 'second'" 
                    :class="tab === 'second' ? 'border-b-2 border-indigo-400' : ''"
                    class="px-2 py-1"
                >
                    Skills I want to Improve
                </button>
                
            </div>

     
            <!-- Skills I've Demonstrated -->
            <div x-show="tab === 'first'" class="mb-8">
                <h3 class="text-lg font-medium mb-4 text-gray-800">Recent Skills Demonstrated</h3>

                @if($userSkillPractices->isEmpty())
                    <div class="bg-white rounded-lg p-6 shadow border border-gray-200 text-center">
                        <div class="text-gray-400 mb-2">
                            <x-heroicon-o-academic-cap class="inline-block w-12 h-12" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-800">No skills recorded yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Start by evaluating your performance to track skills you've demonstrated.</p>
                    </div>
                @else
                    @php
                        // Group by Skill Area
                        $areaGroups = $userSkillPractices->groupBy(fn($item) => $item->skill->skillArea->id);
                    @endphp

                    @foreach($areaGroups as $areaId => $practicesByArea)
                        @php
                            $area = $practicesByArea->first()->skill->skillArea;
                            $color = $colors[$area->id] ?? '#888888';

                            // Group by individual skill
                            $skillGroups = $practicesByArea->groupBy(fn($item) => $item->skill->name);
                        @endphp

                        <div class="border rounded-lg overflow-hidden bg-white shadow mb-4">
                            <!-- Skill Area Header -->
                            <div class="flex items-center p-4 bg-white font-bold text-gray-900" style="border-top: 4px solid {{ $area->color }}">
                                <span>{{ $area->name }}</span>
                            </div>

                            <div class="pl-8 pr-4 py-3 bg-gray-50 space-y-2">
                                @foreach($skillGroups as $skillName => $practicesBySkill)
                                    @php
                                        $skill = $practicesBySkill->first()->skill;

                                        // Group descriptions by text
                                        $descriptionGroups = $practicesBySkill->groupBy(fn($p) => $p->practice->description);

                                        // Total count for header (sum of all children counts)
                                        $totalCount = $descriptionGroups->reduce(fn($carry, $group) => $carry + $group->count(), 0);
                                    @endphp

                                    <div class="mb-2">
                                        <!-- Header with total count -->
                                        <div class="flex items-center cursor-pointer text-gray-800" x-data="{ open: false }" @click="open = !open">
                                            <span class="font-bold mr-4" style="color: {{ $color }}; font-size:25px">{{ $totalCount }}</span>
                                            <span class="font-medium text-base" style="font-size:18px;text-width:bold;">{{ $skillName }}</span>

                                            {{-- @if($totalCount > 1)
                                                <svg x-show="!open" class="h-5 w-5 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                                <svg x-show="open" class="h-5 w-5 text-gray-400  ml-[100px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                </svg>
                                            @endif --}}
                                        </div>

                                        <!-- Child descriptions -->
                                        <div x-show="open" x-transition class="pl-8 mt-1 space-y-1">
                                            @foreach($descriptionGroups as $desc => $descGroup)
                                                @php
                                                    $descCount = $descGroup->count();
                                                @endphp
                                                <div x-data="{ openChild: false }">
                                                    <div class="flex items-center cursor-pointer" @click="openChild = !openChild">
                                                        <span style="color: {{ $color }};" class="font-bold mr-4">{{ $descCount }}</span>
                                                        <span class="text-gray-800">{{ $desc }}</span>
                                                        {{-- @if($descCount > 1) --}}
                                                            <svg x-show="!openChild" class="h-5 w-5 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                            <svg x-show="openChild" class="h-5 w-5 text-gray-400 ml-auto " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                            </svg>
                                                        {{-- @endif --}}
                                                    </div>

                                                    <div x-show="openChild" x-transition class="pl-8 mt-1 space-y-1">
                                                        @foreach($descGroup as $practice)
                                                            <div class="p-2 bg-gray-100 rounded text-sm text-gray-600">
                                                                <div class="text-sm text-gray-500">{{ $practice->practice->description }}</div>
                                                                <div class="text-xs text-gray-400 mt-1">Selected on {{ $practice->selected_at ? $practice->selected_at->format('M d, Y') : 'Unknown date' }}</div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            
            

            <!-- Skills I Want to Improve -->

           

           <div x-show="tab === 'second'" class="mb-8">
                <h3 class="text-lg font-medium mb-4 text-gray-800">Skills I Want to Improve</h3>

                @if($userFuturePractices->isEmpty())
                    <div class="bg-white rounded-lg p-6 shadow border border-gray-200 text-center">
                        <div class="text-gray-400 mb-2">
                            <x-heroicon-o-arrow-trending-up class="inline-block w-12 h-12" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-800">No improvement areas recorded yet</h3>
                    </div>

                @else
                    @php
                        // Group by Skill Area
                        $areaGroups = $userFuturePractices->groupBy(fn($item) => $item->skill->skillArea->id);
                    @endphp

                    @foreach($areaGroups as $areaId => $practicesByArea)
                        @php
                            $area = $practicesByArea->first()->skill->skillArea;
                            $color = $colors[$area->id] ?? '#888888';

                            // Group by Skill
                            $skillGroups = $practicesByArea->groupBy(fn($item) => $item->skill->id);
                        @endphp

                        <div class="border rounded-lg overflow-hidden bg-white shadow mb-4">

                            <!-- Area Header -->
                            <div class="p-4 font-bold text-gray-900" style="border-top: 4px solid {{ $area->color }}">
                                {{ $area->name }}
                            </div>

                            <div class="pl-8 pr-4 py-3 bg-gray-50 space-y-2">

                                @foreach($skillGroups as $skillId => $practicesBySkill)
                                    @php
                                        $skill = $practicesBySkill->first()->skill;

                                        // Group by description
                                        $descriptionGroups = $practicesBySkill->groupBy(fn($p) => $p->practice->description);

                                        // Total count for skill header
                                        $totalCount = $descriptionGroups->reduce(fn($carry, $group) => $carry + $group->count(), 0);

                                        // Demo count from first tab (optional)
                                        $demoCount = $skillFrequency[$skillId] ?? 0;
                                    @endphp

                                    <div class="mb-2">

                                        <!-- Skill Header -->
                                        <div class="flex items-center cursor-pointer text-gray-800"
                                            x-data="{ open: false }"
                                            @click="open = !open">

                                            <span class="font-bold mr-4" style="color: {{ $color }}; font-size:25px">
                                                {{ $totalCount }}
                                            </span>

                                            <span class="font-medium text-base" style="font-size:18px;">
                                                {{ $skill->name }}
                                            </span>
                                        </div>

                                        <!-- Description Groups -->
                                        <div x-show="open" x-transition class="pl-8 mt-1 space-y-1">

                                            @foreach($descriptionGroups as $desc => $descGroup)
                                                @php $descCount = $descGroup->count(); @endphp

                                                <div x-data="{ openChild: false }">

                                                    <div class="flex items-center cursor-pointer"
                                                        @click="openChild = !openChild">

                                                        <span class="font-bold mr-4" style="color: {{ $color }}">
                                                            {{ $descCount }}
                                                        </span>

                                                        <span class="text-gray-800">
                                                            {{ $desc }}
                                                        </span>

                                                        <!-- Arrows -->
                                                        <svg x-show="!openChild" class="h-5 w-5 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                        </svg>
                                                        <svg x-show="openChild" class="h-5 w-5 text-gray-400 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                                        </svg>
                                                    </div>

                                                    <!-- Individual Practices -->
                                                    <div x-show="openChild" x-transition class="pl-8 mt-1 space-y-1">
                                                        @foreach($descGroup as $practice)
                                                            <div class="p-2 bg-gray-100 rounded text-sm text-gray-600">
                                                                <div class="text-sm text-gray-500">
                                                                    {{ $practice->practice->description }}
                                                                </div>
                                                                <div class="text-xs text-gray-400 mt-1">
                                                                    Selected on {{ $practice->selected_at ? $practice->selected_at->format('M d, Y') : 'Unknown date' }}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>

                                @endforeach

                            </div>
                        </div>

                    @endforeach

                @endif
            </div>

        </div>

       
    </x-filament::section>
</x-filament-panels::page>



{{-- 
DELETE FROM user_skill_practices WHERE skill_id = 31; --}}
