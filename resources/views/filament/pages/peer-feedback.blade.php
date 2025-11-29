<x-filament-panels::page class="bg-white text-gray-800">
    <x-filament::section class="bg-[#f8faf7] border-gray-200">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 mx-5">Peer Feedback</h2>
            <p class="text-gray-500 mx-5">Feedback received from and given to your team members</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="rounded-lg bg-white p-4 shadow border border-gray-200">
                <div class="flex items-center">
                    <div class="rounded-full bg-primary-900 p-2.5 mr-5">
                        <x-heroicon-o-arrow-down-on-square class="h-6 w-6 text-primary-400" />
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-800">{{ $totalReceived }}</p>
                        <p class="text-sm text-gray-500">Feedback Received from Peers</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-4 shadow border border-gray-200">
                <div class="flex items-center">
                    <div class="rounded-full bg-success-900 p-2.5 mr-5">
                        <x-heroicon-o-arrow-up-on-square class="h-6 w-6 text-success-400" />
                    </div>
                    <div>
                        <p class="text-lg font-bold text-gray-800">{{ $totalGiven }}</p>
                        <p class="text-sm text-gray-500">Feedback Given to Peers</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-lg font-medium mb-4 text-gray-800">Feedback Received From Peers</h3>

            @if(empty($feedbackFromPeers))
                <div class="bg-white rounded-lg p-6 shadow border border-gray-200 text-center">
                    <div class="text-gray-400 mb-2">
                        <x-heroicon-o-chat-bubble-left-right class="inline-block w-12 h-12" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-800">No peer feedback yet</h3>
                    <p class="mt-1 text-sm text-gray-500">You haven't received any feedback from your peers yet.</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($feedbackFromPeers as $skillId => $feedbackItems)
                        <div class="bg-white rounded-lg p-6 shadow border border-gray-200">
                            @php
                                $items = collect($feedbackItems);
                                $firstItem = $items->first();
                                $practice = optional($firstItem)->practice;
                                $skill = optional($practice)->skill;
                                $skillArea = optional($skill)->skillArea;
                            @endphp
                            <h3 class="text-lg font-medium mb-4 border-l-4 pl-2 text-gray-800" style="border-color: {{ $colors[optional($skillArea)->id] ?? '#888' }}">
                                {{ optional($skillArea)->name }}: {{ optional($skill)->name }}
                            </h3>

                            <div class="space-y-4">
                                @foreach($feedbackItems as $feedback)
                                    <div class="border rounded-lg p-4 {{ optional($feedback)->is_positive ? 'bg-green-900 border-green-700 text-gray-800' : 'bg-amber-900 border-amber-700 text-gray-800' }}">
                                        <div class="flex justify-between items-start">
                                            <div class="font-medium">
                                                {{ optional($feedback)->is_positive ? 'Positive Feedback' : 'Area for Improvement' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ optional(optional($feedback)->created_at)->format('M d, Y') }}
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-100">{{ optional(optional($feedback)->practice)->description }}</p>
                                        </div>
                                        <div class="mt-3 pt-2 border-t border-gray-200 flex justify-between text-xs text-gray-500">
                                            <div>From: {{ optional(optional($feedback)->sender)->name }}</div>
                                            <div>Team: {{ optional(optional($feedback)->team)->name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <h3 class="text-lg font-medium mb-4 text-gray-800">Feedback Given To Peers</h3>

            @if(collect($feedbackToPeers)->isEmpty())
                <div class="bg-white rounded-lg p-6 shadow border border-gray-200 text-center">
                    <div class="text-gray-400 mb-2">
                        <x-heroicon-o-chat-bubble-left-right class="inline-block w-12 h-12" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-800">No feedback given yet</h3>
                    <p class="mt-1 text-sm text-gray-500">You haven't given any feedback to your peers yet.</p>
                    <a href="{{ route('filament.admin.pages.peer-evaluation') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-primary-700">
                        Evaluate Team Members
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($feedbackToPeers as $receiverId => $feedbackItems)
                        <div class="bg-white rounded-lg p-6 shadow border border-gray-200">
                            @php
                                $items = collect($feedbackItems);
                                $firstItem = $items->first();
                                $practice = optional($firstItem)->practice;
                                $skill = optional($practice)->skill;
                                $skillArea = optional($skill)->skillArea;
                            @endphp
                            <h3 class="text-lg font-medium mb-4 text-gray-800">
                                Feedback for: {{ optional(optional($firstItem)->receiver)->name }}
                            </h3>

                            <div class="space-y-4">
                                @foreach($feedbackItems as $feedback)
                                    <div class="border rounded-lg p-4 {{ optional($feedback)->is_positive ? 'bg-green-900 border-green-700 text-gray-800' : 'bg-amber-900 border-amber-700 text-gray-800' }}">
                                        <div class="flex justify-between items-start">
                                            <div class="font-medium">
                                                {{ optional($feedback)->is_positive ? 'Positive Feedback' : 'Area for Improvement' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ optional(optional($feedback)->created_at)->format('M d, Y') }}
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-100">{{ optional($skillArea)->name }}: {{ optional($skill)->name }}</p>
                                            <p class="text-sm text-gray-100 mt-1">{{ optional(optional($feedback)->practice)->description }}</p>
                                        </div>
                                        <div class="mt-3 pt-2 border-t border-gray-200 flex justify-between text-xs text-gray-500">
                                            <div>Team: {{ optional(optional($feedback)->team)->name }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-6">
            <x-filament::button
                color="primary"
                tag="a"
                :href="route('filament.admin.pages.peer-evaluation')"
            >
                Evaluate Team Member
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-panels::page>
