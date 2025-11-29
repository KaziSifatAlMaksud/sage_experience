<x-filament-panels::page class="text-gray-800">
    <x-filament::section class="bg-[#f8faf7] border-gray-200">
        <div class="mb-6">
            <!-- <h2 class="text-xl font-bold text-gray-800">Review Skill Practices</h2> -->
            <p class="text-gray-500">Browse through all skill areas, skills, and practices available for development</p>
        </div>

        <div class="space-y-4">
            @foreach($skillAreas as $area)
                <div class="border rounded-lg overflow-hidden"
                     style="border-left: 4px solid {{ $area->color ?? '#666' }}">
                    <div wire:click="toggleSkillArea({{ $area->id }})"
                         class="flex justify-between items-center p-4 bg-white cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center gap-4">
                            <h3 class="font-bold text-lg">{{ $area->name }}</h3>
                        </div>
                        <div>
                            @if($expandedSkillArea === $area->id)
                                <x-heroicon-m-chevron-up class="w-5 h-5 text-gray-500" />
                            @else
                                <x-heroicon-m-chevron-down class="w-5 h-5 text-gray-500" />
                            @endif
                        </div>
                    </div>

                    @if($expandedSkillArea === $area->id)
                        <div class="border-t border-gray-200">
                            <div class="pl-8 pr-4 py-3 bg-gray-50">
                                <p class="text-sm text-gray-600">{{ $area->description }}</p>
                            </div>

                            <div class="pb-2">
                                @foreach($area->skills as $skill)
                                    <div class="border-t border-gray-200 ml-4">
                                        <div wire:click="toggleSkill({{ $skill->id }})"
                                             class="flex justify-between items-center p-3 cursor-pointer hover:bg-gray-50">
                                            <div class="flex items-center gap-4">
                                                <div class="w-1.5 h-1.5 rounded-full mr-4" style="background-color: {{ $area->color ?? '#666' }}"></div>                                                <h4 class="font-medium">{{ $skill->name }}</h4>
                                            </div>
                                            <div>
                                                @if($expandedSkill === $skill->id)
                                                    <x-heroicon-m-chevron-up class="w-4 h-4 text-gray-500" />
                                                @else
                                                    <x-heroicon-m-chevron-down class="w-4 h-4 text-gray-500" />
                                                @endif
                                            </div>
                                        </div>

                                        @if($expandedSkill === $skill->id)
                                            <div class="border-t border-gray-100">
                                                <div class="p-3 pl-12 bg-gray-50">
                                                    <p class="text-sm text-gray-600 mb-2">{{ $skill->description }}</p>

                                                    @if($skill->practices->isNotEmpty())
                                                        <p class="font-medium text-sm mb-2">Practices:</p>
                                                        <ul class="space-y-2 pl-3">
                                                            @foreach($skill->practices as $practice)
                                                                <li class="flex items-start">
                                                                    <div class="w-1 h-1 rounded-full mt-2 mr-4" style="background-color: {{ $area->color ?? '#666' }}"></div>
                                                                    <span class="text-sm">{{ $practice->description }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-sm text-gray-500 italic">No practices defined for this skill.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-panels::page>
