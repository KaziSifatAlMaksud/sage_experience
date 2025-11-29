<x-filament::page  class="pt-0">
    <div style="min-height:88vh; padding-top:-20px;">
    {{-- Custom Hello Message --}}
  <div class="flex items-center justify-between bg-white p-6 pt-0 rounded-xl shadow mb-6">
    <div class="flex items-center space-x-6">
        {{-- Circle avatar --}}
        <div style="background-color:black;" class="w-12 h-12 text-2xl rounded-full bg-black text-white flex items-center justify-center font-semibold">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->name, -1)) }}
        </div>

        {{-- Welcome text --}}
        <div class="">
            <div class="text-lg font-semibold">Welcome</div>
            <div class="text-sm text-gray-600">{{ auth()->user()->name }}</div>
        </div>
    </div>

    {{-- Sign out button --}}
    <form method="post" action="{{ route('profile.destroy') }}">
        @csrf
        <button
            type="submit"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 transition"
        >
            <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5 mr-2" />
            Sign out
        </button>
    </form>
</div>

    {{-- Styled Header Actions --}}
    @if(auth()->user()->hasRole('student'))
        <div class="flex flex-col md:flex-row gap-4 mb-6">
    <a
        href="{{ route('filament.admin.pages.skill-practice') }}"
        class="flex-1 inline-flex text-center items-center justify-center px-6 py-3 bg-green-800 text-white font-semibold rounded-lg shadow hover:bg-green-900 transition"
        style="background-color: #3a5531;"
    >
        <x-heroicon-m-academic-cap class="w-5 h-5 mr-2" />
        Evaluate My Latest Performance
    </a>

    <a
        href="{{ route('filament.admin.pages.peer-evaluation') }}"
        class="flex-1 inline-flex items-center text-center justify-center px-6 py-3 bg-green-800 text-white font-semibold rounded-lg shadow hover:bg-green-900 transition"
        style="background-color: #3a5531;"
    >
        <x-heroicon-m-user-group class="w-5 h-5 mr-2" />
        Evaluate Team Member Performance
    </a>

    <a
        href="{{ route('filament.admin.pages.student-feedback-dashboard') }}"
        class="flex-1 inline-flex items-center text-center justify-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg shadow hover:bg-green-900 transition"
        style="background-color: #3a5531;"
    >
        <x-heroicon-m-user-group class="w-5 h-5 mr-2" />
        Review My Feedback
    </a>
</div>
    @endif

    <div>

</x-filament::page>