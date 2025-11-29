<div class="p-4 bg-gray-50">
    <p><strong>Description:</strong> {{ $record->description }}</p>
    <p><strong>Created At:</strong> {{ $record->created_at->format('Y-m-d H:i') }}</p>
    <p><strong>Updated At:</strong> {{ $record->updated_at->format('Y-m-d H:i') }}</p>
</div>
