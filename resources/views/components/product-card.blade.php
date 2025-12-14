{{-- Product Card Component --}}
@props(['product'])

<article
    class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
    {{-- Image Container --}}
    <div class="relative overflow-hidden aspect-square bg-gradient-to-br from-gray-100 to-gray-200">
        @if($product->mainImage)
            <img src="{{ asset('storage/' . $product->mainImage->path) }}"
                alt="{{ $product->mainImage->alt_text ?? $product->name }}"
                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700"
                loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-2">
            @if($product->discount > 0)
                <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-lg">
                    -{{ $product->discount }}%
                </span>
            @endif
            @if($product->featured)
                <span
                    class="px-3 py-1 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold rounded-full shadow-lg">
                    Destacado
                </span>
            @endif
            @if($product->stock <= 5 && $product->stock > 0)
                <span class="px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-full shadow-lg">
                    ¡Últimas unidades!
                </span>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div
            class="absolute top-3 right-3 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button
                class="w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
            <a href="{{ route('product.show', $product) }}"
                class="w-10 h-10 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
        </div>

        {{-- Out of Stock Overlay --}}
        @if($product->stock <= 0)
            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                <span class="px-6 py-2 bg-gray-900 text-white font-bold rounded-full">Agotado</span>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-5">
        {{-- Brand & Category --}}
        <div class="flex items-center justify-between mb-2">
            <span
                class="text-xs font-medium text-indigo-600 uppercase tracking-wider">{{ $product->brand->name ?? 'Sin marca' }}</span>
            <span class="text-xs text-gray-400">{{ $product->category->name ?? '' }}</span>
        </div>

        {{-- Name --}}
        <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-indigo-600 transition-colors">
            <a href="{{ route('product.show', $product) }}">{{ $product->name }}</a>
        </h3>

        {{-- Rating --}}
        @if($product->rating_avg > 0)
            <div class="flex items-center gap-1 mb-3">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="w-4 h-4 {{ $i <= $product->rating_avg ? 'text-amber-400' : 'text-gray-200' }}"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                @endfor
                <span class="text-xs text-gray-500 ml-1">({{ number_format($product->rating_avg, 1) }})</span>
            </div>
        @endif

        {{-- Sizes Preview --}}
        @php
            $sizes = is_array($product->sizes_available) ? $product->sizes_available : json_decode($product->sizes_available, true) ?? [];
        @endphp
        @if(count($sizes) > 0)
            <div class="flex flex-wrap gap-1 mb-3">
                @foreach(array_slice($sizes, 0, 5) as $size)
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $size }}</span>
                @endforeach
                @if(count($sizes) > 5)
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-400 text-xs rounded">+{{ count($sizes) - 5 }}</span>
                @endif
            </div>
        @endif

        {{-- Price --}}
        <div class="flex items-center justify-between mt-4">
            <div class="flex items-baseline gap-2">
                @if($product->discount > 0)
                    <span class="text-2xl font-bold text-indigo-600">S/ {{ number_format($product->final_price, 2) }}</span>
                    <span class="text-sm text-gray-400 line-through">S/ {{ number_format($product->price, 2) }}</span>
                @else
                    <span class="text-2xl font-bold text-gray-900">S/ {{ number_format($product->price, 2) }}</span>
                @endif
            </div>
        </div>

        {{-- Add to Cart Button --}}
        @if($product->stock > 0)
            <form action="{{ route('cart.add') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                @if(count($sizes) > 0)
                    <input type="hidden" name="size" value="{{ $sizes[0] }}">
                @endif
                <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Agregar al Carrito
                </button>
            </form>
        @else
            <button disabled class="w-full mt-4 py-3 bg-gray-200 text-gray-500 font-semibold rounded-xl cursor-not-allowed">
                Sin Stock
            </button>
        @endif
    </div>
</article>