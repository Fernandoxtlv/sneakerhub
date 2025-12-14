<x-layouts.app title="Catálogo">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <aside class="lg:w-64 shrink-0" x-data="filters">
                <!-- Mobile filter toggle -->
                <button @click="toggle"
                    class="lg:hidden w-full btn btn-outline mb-4 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filtros
                </button>

                <div :class="isOpen ? 'block' : 'hidden lg:block'" class="bg-white rounded-2xl shadow-lg p-6">
                    <form action="{{ route('catalog') }}" method="GET">
                        <!-- Categories -->
                        @php
                            $selectedCategoryId = request('category');
                            if (is_object($selectedCategoryId) && isset($selectedCategoryId->id)) {
                                $selectedCategoryId = $selectedCategoryId->id;
                            }

                            $selectedBrandId = request('brand');
                            if (is_object($selectedBrandId) && isset($selectedBrandId->id)) {
                                $selectedBrandId = $selectedBrandId->id;
                            }
                        @endphp
                        <div class="filter-section">
                            <h3 class="filter-title">
                                Categorías
                            </h3>
                            <div class="space-y-2">
                                @foreach($categories as $cat)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="category" value="{{ $cat->id }}" {{ $selectedCategoryId == $cat->id ? 'checked' : '' }}
                                            class="text-primary-600 focus:ring-primary-500">
                                        <span class="text-sm text-gray-600">{{ $cat->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Brands -->
                        <div class="filter-section">
                            <h3 class="filter-title">
                                Marcas
                            </h3>
                            <div class="space-y-2">
                                @foreach($brands as $b)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="brand" value="{{ $b->id }}" {{ $selectedBrandId == $b->id ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500">
                                        <span class="text-sm text-gray-600">{{ $b->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div class="filter-section">
                            <h3 class="filter-title">Precio</h3>
                            <div class="flex items-center gap-2">
                                <input type="number" name="price_min" value="{{ request('price_min') }}"
                                    placeholder="Mín" class="form-input text-sm py-2">
                                <span class="text-gray-400">-</span>
                                <input type="number" name="price_max" value="{{ request('price_max') }}"
                                    placeholder="Máx" class="form-input text-sm py-2">
                            </div>
                        </div>

                        <!-- Size -->
                        <div class="filter-section">
                            <h3 class="filter-title">Talla</h3>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($sizes as $size)
                                    <label class="size-btn cursor-pointer text-center">
                                        <input type="radio" name="size" value="{{ $size }}" {{ request('size') == $size ? 'checked' : '' }} class="hidden">
                                        <span>{{ $size }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="filter-section">
                            <h3 class="filter-title">Género</h3>
                            <div class="space-y-2">
                                @foreach(['men' => 'Hombre', 'women' => 'Mujer', 'unisex' => 'Unisex', 'kids' => 'Niños'] as $value => $label)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="gender" value="{{ $value }}" {{ request('gender') == $value ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500">
                                        <span class="text-sm text-gray-600">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-full mt-4">Aplicar Filtros</button>
                        <a href="{{ route('catalog') }}" class="btn btn-outline w-full mt-2">Limpiar</a>
                    </form>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1">
                <!-- Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            @if(isset($category))
                                {{ $category->name }}
                            @elseif(isset($brand))
                                {{ $brand->name }}
                            @elseif(isset($query) && $query)
                                Resultados para "{{ $query }}"
                            @else
                                Catálogo
                            @endif
                        </h1>
                        <p class="text-gray-500 mt-1">{{ $products->total() }} productos encontrados</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="text-sm text-gray-600">Ordenar por:</label>
                        <select name="sort" onchange="window.location.href = this.value"
                            class="form-input py-2 text-sm pr-10">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Más recientes</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Precio: menor a mayor</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Precio: mayor a menor</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Más vendidos</option>
                        </select>
                    </div>
                </div>

                <!-- Products -->
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="product-card group">
                                <div class="product-card-image">
                                    <a href="{{ route('product.show', $product) }}">
                                        @if($product->mainImage)
                                            <img src="{{ Storage::url($product->mainImage->path_medium ?? $product->mainImage->path) }}"
                                                alt="{{ $product->name }}">
                                        @else
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </a>

                                    <!-- Badges -->
                                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                                        @if($product->is_new)
                                            <span class="badge bg-primary-500 text-white">Nuevo</span>
                                        @endif
                                        @if($product->has_discount)
                                            <span class="badge bg-red-500 text-white">-{{ $product->discount }}%</span>
                                        @endif
                                        @if($product->featured)
                                            <span class="badge bg-yellow-500 text-white">⭐ Destacado</span>
                                        @endif
                                    </div>

                                    <!-- Quick Add Button -->
                                    <div class="product-card-overlay"></div>
                                    <div class="product-card-actions">
                                        <button @click="addToCart({{ $product->id }})" class="flex-1 btn btn-primary btn-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                            Agregar
                                        </button>
                                        <a href="{{ route('product.show', $product) }}"
                                            class="btn bg-white text-gray-700 btn-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <a href="{{ route('brand.show', $product->brand) }}"
                                        class="text-xs font-semibold text-primary-600 uppercase tracking-wider mb-1 block hover:text-primary-700">
                                        {{ $product->brand->name }}
                                    </a>
                                    <h3 class="font-semibold text-gray-900 mb-2">
                                        <a href="{{ route('product.show', $product) }}"
                                            class="hover:text-primary-600 transition-colors">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-xl font-bold text-gray-900">{{ $product->formatted_current_price }}</span>
                                        @if($product->has_discount)
                                            <span class="text-sm text-gray-400 line-through">{{ $product->formatted_price }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No encontramos productos</h3>
                        <p class="text-gray-500 mb-6">Intenta con otros filtros o busca algo diferente</p>
                        <a href="{{ route('catalog') }}" class="btn btn-primary">Ver todo el catálogo</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>