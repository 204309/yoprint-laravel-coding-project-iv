@props(['status'])

@php
    // Normalize status casing (e.g.: "Completed", "completed", "COMPLETED")
    $normalized = strtolower($status);

    $classes = match($normalized) {
        'completed' => 'bg-green-700/60 text-emerald-50',
        'processing' => 'bg-yellow-600/60 text-yellow-50',
        'failed' => 'bg-red-700/60 text-red-50',
        default => 'bg-gray-800/60 text-gray-100',
    };
@endphp

<span class="rounded-full px-3 py-1.5 font-medium {{ $classes }}">
    {{ ucfirst($status) }}
</span>
