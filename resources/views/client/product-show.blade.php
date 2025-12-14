<x-layouts.app :title="$product->name">
    <div class="container mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-8">
            <a href="{{ route('home') }}" class="hover:text-primary-600">Inicio</a>
            <span>/</span>
            <a href="{{ route('catalog') }}" class="hover:text-primary-600">Catálogo</a>
            <span>/</span>
            <a href="{{ route('category.show', $product->category) }}"
                class="hover:text-primary-600">{{ $product->category->name }}</a>
            <span>/</span>
            <span class="text-gray-900">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12" x-data="{ selectedSize: null, quantity: 1 }">
            <!-- Image Gallery -->
            <div class="space-y-4" x-data="productGallery"
                x-init="images = {{ json_encode($product->images->map(fn($img) => ['url' => Storage::url($img->path), 'thumb' => Storage::url($img->path_thumb ?? $img->path)])) }}">
                <!-- Main Image -->
                <div class="relative aspect-square bg-gray-100 rounded-2xl overflow-hidden cursor-zoom-in"
                    @click="openLightbox()">
                    @if($product->images->count() > 0)
                        <template x-for="(image, index) in images" :key="index">
                            <img :src="image.url" :alt="'{{ $product->name }} - Imagen ' + (index + 1)"
                                x-show="activeIndex === index" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                class="absolute inset-0 w-full h-full object-cover object-center">
                        </template>
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-32 h-32 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    <!-- Badges -->
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        @if($product->is_new)
                            <span class="badge bg-primary-500 text-white text-sm px-4 py-1">Nuevo</span>
                        @endif
                        @if($product->has_discount)
                            <span class="badge bg-red-500 text-white text-sm px-4 py-1">-{{ $product->discount }}%</span>
                        @endif
                    </div>
                </div>

                <!-- Thumbnails -->
                @if($product->images->count() > 1)
                    <div class="flex gap-3 overflow-x-auto pb-2">
                        @foreach($product->images as $index => $image)
                            <button @click="setActive({{ $index }})"
                                :class="activeIndex === {{ $index }} ? 'ring-2 ring-primary-500' : 'ring-1 ring-gray-200'"
                                class="shrink-0 w-20 h-20 rounded-xl overflow-hidden transition-all">
                                <img src="{{ Storage::url($image->path_thumb ?? $image->path) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Lightbox -->
                <div x-show="lightboxOpen" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" @keydown.escape.window="closeLightbox()" class="lightbox active">
                    <button @click="closeLightbox()" class="absolute top-6 right-6 text-white hover:text-gray-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <button @click="prev()"
                        class="absolute left-6 top-1/2 -translate-y-1/2 text-white hover:text-gray-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="next()"
                        class="absolute right-6 top-1/2 -translate-y-1/2 text-white hover:text-gray-300">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <template x-for="(image, index) in images" :key="index">
                        <img :src="image.url" x-show="activeIndex === index"
                            class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg">
                    </template>
                </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <div>
                    <a href="{{ route('brand.show', $product->brand) }}"
                        class="text-sm font-semibold text-primary-600 uppercase tracking-wider mb-2 block hover:text-primary-700">
                        {{ $product->brand->name }}
                    </a>
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

                    <!-- Rating -->
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($product->rating_avg) ? 'text-yellow-400' : 'text-gray-300' }}"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <span class="text-sm text-gray-500">({{ $product->rating_count }} reseñas)</span>
                        <span class="text-sm text-gray-400">|</span>
                        <span class="text-sm text-gray-500">{{ $product->views_count }} visitas</span>
                    </div>
                </div>

                <!-- Price -->
                <div class="flex items-end gap-4">
                    <span class="text-4xl font-bold text-gray-900">{{ $product->formatted_current_price }}</span>
                    @if($product->has_discount)
                        <span class="text-xl text-gray-400 line-through">{{ $product->formatted_price }}</span>
                        <span class="badge bg-red-100 text-red-700">Ahorras {{ config('sneakerhub.currency.symbol', 'S/') }}
                            {{ number_format($product->price - $product->current_price, 2) }}</span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="flex items-center gap-2">
                    @if($product->is_in_stock)
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <span class="text-sm text-green-700 font-medium">En stock ({{ $product->stock }} disponibles)</span>
                    @else
                        <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                        <span class="text-sm text-red-700 font-medium">Agotado</span>
                    @endif
                </div>

                <!-- Description -->
                <p class="text-gray-600 leading-relaxed">{{ $product->short_description ?? $product->description }}</p>

                <!-- Size Selector -->
                @if($product->sizes_available && count($product->sizes_available) > 0)
                    <div>
                        <label class="form-label">Selecciona tu talla:</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->sizes_available as $size)
                                <button type="button" @click="selectedSize = '{{ $size }}'"
                                    :class="selectedSize === '{{ $size }}' ? 'border-primary-500 bg-primary-500 text-white' : ''"
                                    class="size-btn">
                                    {{ $size }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quantity -->
                <div>
                    <label class="form-label">Cantidad:</label>
                    <div class="flex items-center gap-2">
                        <button @click="quantity > 1 ? quantity-- : null" class="qty-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <input type="number" x-model="quantity" min="1" max="{{ $product->stock }}"
                            class="w-20 text-center form-input py-2">
                        <button @click="quantity < {{ $product->stock }} ? quantity++ : null" class="qty-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Add to Cart Form -->
                <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" :value="quantity">
                    <input type="hidden" name="size" :value="selectedSize">

                    <button type="submit" @if(!$product->is_in_stock) disabled @endif
                        class="btn btn-primary btn-lg w-full group disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5 mr-2 group-hover:animate-bounce" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        {{ $product->is_in_stock ? 'Agregar al Carrito' : 'Producto Agotado' }}
                    </button>
                </form>

                <!-- Product Details -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">SKU:</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $product->sku }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Categoría:</span>
                            <a href="{{ route('category.show', $product->category) }}"
                                class="font-medium text-primary-600 ml-2 hover:text-primary-700">{{ $product->category->name }}</a>
                        </div>
                        @if($product->color)
                            <div>
                                <span class="text-gray-500">Color:</span>
                                <span class="font-medium text-gray-900 ml-2">{{ $product->color }}</span>
                            </div>
                        @endif
                        @if($product->material)
                            <div>
                                <span class="text-gray-500">Material:</span>
                                <span class="font-medium text-gray-900 ml-2">{{ $product->material }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span class="text-sm text-gray-600">Envío gratis en compras mayores a S/ 300</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span class="text-sm text-gray-600">Garantía de autenticidad 100%</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span class="text-sm text-gray-600">Paga con efectivo o Yape</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Description -->
        @if($product->description)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Descripción</h2>
                <div class="prose prose-gray max-w-none">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        @endif

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Productos Relacionados</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <div class="product-card group">
                            <div class="product-card-image aspect-square">
                                <a href="{{ route('product.show', $related) }}">
                                    @if($related->mainImage)
                                        <img src="{{ Storage::url($related->mainImage->path_medium ?? $related->mainImage->path) }}"
                                            alt="{{ $related->name }}">
                                    @else
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                            </div>
                            <div class="p-4">
                                <span
                                    class="text-xs font-semibold text-primary-600 uppercase">{{ $related->brand->name }}</span>
                                <h3 class="font-semibold text-gray-900 mt-1">
                                    <a href="{{ route('product.show', $related) }}"
                                        class="hover:text-primary-600">{{ $related->name }}</a>
                                </h3>
                                <p class="text-lg font-bold text-gray-900 mt-2">{{ $related->formatted_current_price }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>