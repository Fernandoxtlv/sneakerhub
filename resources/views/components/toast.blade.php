{{-- Toast/Alert Component --}}
@props(['type' => 'info', 'message' => ''])

@php
    $bgColors = [
        'success' => 'bg-gradient-to-r from-emerald-500 to-green-600',
        'error' => 'bg-gradient-to-r from-red-500 to-rose-600',
        'warning' => 'bg-gradient-to-r from-amber-500 to-orange-600',
        'info' => 'bg-gradient-to-r from-blue-500 to-indigo-600',
    ];
    $icons = [
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
        'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
        'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
    ];
@endphp

<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform translate-y-4"
    x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed bottom-6 right-6 z-50 max-w-md">
    <div class="{{ $bgColors[$type] }} text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icons[$type] !!}
            </svg>
        </div>
        <p class="font-medium">{{ $message ?: $slot }}</p>
        <button @click="show = false" class="flex-shrink-0 ml-2 hover:opacity-75 transition-opacity">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>