<div class="border rounded-lg shadow-sm p-4 relative bg-white">
    <div class="border-l-4 pl-4" style="border-color: {{ $skillAreaColor ?? '#000' }}">
        <h2 class="font-bold text-lg mb-1">Skill Area</h2>
        <div class="font-semibold">{{ $skillArea ?? 'N/A' }}</div>
        <div class="mt-2 bg-gray-100 p-2 rounded">
            {{ $skillName ?? 'No skill selected' }}
        </div>
    </div>
</div>
