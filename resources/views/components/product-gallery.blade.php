{{-- Product Gallery Component --}}
@props(['images', 'productName'])

<div x-data="{ 
    activeImage: 0,
    lightbox: false,
    images: {{ Js::from($images->map(fn($img) => asset('storage/' . $img->filename))) }}
}" class="space-y-4">

    {{-- Main Image --}}
    <div class="relative aspect-square bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl overflow-hidden group cursor-zoom-in"
        @click="lightbox = true">
        <template x-for="(image, index) in images" :key="index">
            <img :src="image" :alt="'{{ $productName }} - Imagen ' + (index + 1)" x-show="activeImage === index"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                class="absolute inset-0 w-full h-full object-contain" loading="lazy">
        </template>

        {{-- Zoom Icon --}}
        <div
            class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
            <div class="w-16 h-16 bg-white/90 rounded-full flex items-center justify-center shadow-xl">
                <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                </svg>
            </div>
        </div>

        {{-- Navigation Arrows --}}
        <button @click.stop="activeImage = (activeImage - 1 + images.length) % images.length" x-show="images.length > 1"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button @click.stop="activeImage = (activeImage + 1) % images.length" x-show="images.length > 1"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/90 rounded-full shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-white">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>

    {{-- Thumbnails --}}
    @if($images->count() > 1)
        <div class="grid grid-cols-5 gap-3">
            @foreach($images as $index => $image)
                <button @click="activeImage = {{ $index }}"
                    :class="{ 'ring-2 ring-indigo-500 ring-offset-2': activeImage === {{ $index }} }"
                    class="aspect-square bg-gray-100 rounded-xl overflow-hidden hover:opacity-80 transition-all duration-200">
                    <img src="{{ asset('storage/' . $image->filename) }}" alt="{{ $productName }} - Thumbnail {{ $index + 1 }}"
                        class="w-full h-full object-cover" loading="lazy">
                </button>
            @endforeach
        </div>
    @endif

    {{-- Lightbox Modal --}}
    <div x-show="lightbox" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @keydown.escape.window="lightbox = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 p-4" style="display: none;">

        {{-- Close Button --}}
        <button @click="lightbox = false"
            class="absolute top-6 right-6 text-white hover:text-gray-300 transition-colors z-10">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Image Counter --}}
        <div class="absolute top-6 left-6 text-white font-medium z-10">
            <span x-text="activeImage + 1"></span> / <span x-text="images.length"></span>
        </div>

        {{-- Main Lightbox Image --}}
        <div class="max-w-6xl max-h-[90vh] w-full">
            <template x-for="(image, index) in images" :key="'lb-' + index">
                <img :src="image" :alt="'{{ $productName }} - Imagen ' + (index + 1)" x-show="activeImage === index"
                    x-transition class="max-w-full max-h-[90vh] mx-auto object-contain">
            </template>
        </div>

        {{-- Lightbox Navigation --}}
        <button @click="activeImage = (activeImage - 1 + images.length) % images.length" x-show="images.length > 1"
            class="absolute left-6 top-1/2 -translate-y-1/2 w-14 h-14 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button @click="activeImage = (activeImage + 1) % images.length" x-show="images.length > 1"
            class="absolute right-6 top-1/2 -translate-y-1/2 w-14 h-14 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
</div>