@extends('layouts.admin')

@section('title', $product->name)

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.products.index') }}"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <p class="text-gray-500">SKU: {{ $product->sku }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.products.edit', $product) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white font-medium rounded-xl hover:bg-amber-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Editar
                </a>
                <a href="{{ route('product.show', $product) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 text-white font-medium rounded-xl hover:bg-gray-800 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Ver en Tienda
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Images --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Imágenes</h2>

                    @if($product->images->count() > 0)
                        <div class="space-y-4">
                            <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden">
                                <img src="{{ asset('storage/' . $product->mainImage->path) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover">
                            </div>

                            @if($product->images->count() > 1)
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($product->images as $image)
                                        <div
                                            class="aspect-square bg-gray-100 rounded-lg overflow-hidden {{ $image->is_main ? 'ring-2 ring-indigo-500' : '' }}">
                                            <img src="{{ asset('storage/' . $image->filename) }}" class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="aspect-square bg-gray-100 rounded-xl flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Status Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl border border-gray-100 p-4">
                        <p class="text-sm text-gray-500">Precio</p>
                        <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($product->price, 2) }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4">
                        <p class="text-sm text-gray-500">Stock</p>
                        <p class="text-2xl font-bold {{ $product->stock <= 5 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $product->stock }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4">
                        <p class="text-sm text-gray-500">Descuento</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $product->discount }}%</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4">
                        <p class="text-sm text-gray-500">Estado</p>
                        <span
                            class="inline-flex px-3 py-1 rounded-full text-sm font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $product->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>

                {{-- Information --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información</h2>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">Marca</dt>
                            <dd class="font-medium text-gray-900">{{ $product->brand->name ?? 'Sin marca' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Categoría</dt>
                            <dd class="font-medium text-gray-900">{{ $product->category->name ?? 'Sin categoría' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Color</dt>
                            <dd class="font-medium text-gray-900">{{ $product->color ?? 'No especificado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Rating</dt>
                            <dd class="font-medium text-gray-900">{{ number_format($product->rating_avg, 1) }} / 5.0</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Precio de Costo</dt>
                            <dd class="font-medium text-gray-900">S/ {{ number_format($product->cost_price ?? 0, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Margen</dt>
                            @php
                                $margin = $product->cost_price ? (($product->price - $product->cost_price) / $product->price) * 100 : 0;
                            @endphp
                            <dd class="font-medium text-gray-900">{{ number_format($margin, 1) }}%</dd>
                        </div>
                    </dl>
                </div>

                {{-- Sizes --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tallas Disponibles</h2>

                    @php
                        $sizes = is_array($product->sizes_available) ? $product->sizes_available : json_decode($product->sizes_available, true) ?? [];
                    @endphp

                    @if(count($sizes) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($sizes as $size)
                                <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium">{{ $size }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No hay tallas configuradas</p>
                    @endif
                </div>

                {{-- Description --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Descripción</h2>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>

                {{-- Stock Update --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Actualizar Stock</h2>

                    <form action="{{ route('admin.products.update-stock', $product) }}" method="POST"
                        class="flex items-end gap-4">
                        @csrf
                        @method('PATCH')

                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                            <input type="number" name="stock_change" value="0"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                        </div>

                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                            <select name="reason"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
                                <option value="adjustment">Ajuste manual</option>
                                <option value="purchase">Compra/reposición</option>
                                <option value="return">Devolución</option>
                                <option value="damage">Daño/pérdida</option>
                            </select>
                        </div>

                        <button type="submit"
                            class="px-6 py-2.5 bg-indigo-500 text-white font-medium rounded-xl hover:bg-indigo-600 transition-colors">
                            Actualizar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection