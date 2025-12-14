@extends('layouts.admin')

@section('title', 'Editar: ' . $product->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.products.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Producto</h1>
            <p class="text-gray-500">{{ $product->name }}</p>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Información Básica</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-input name="name" label="Nombre del Producto" :value="old('name', $product->name)" required />
                </div>
                
                <x-input name="sku" label="SKU" :value="old('sku', $product->sku)" required />
                
                <x-input name="slug" label="Slug (URL)" :value="old('slug', $product->slug)" />
                
                <x-select name="brand_id" label="Marca" :options="$brands->pluck('name', 'id')" :selected="old('brand_id', $product->brand_id)" required />
                
                <x-select name="category_id" label="Categoría" :options="$categories->pluck('name', 'id')" :selected="old('category_id', $product->category_id)" required />
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" rows="4" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 bg-gray-50 hover:bg-white focus:bg-white transition-all">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        {{-- Pricing & Stock --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Precio y Stock</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-input type="number" name="price" label="Precio de Venta (S/)" :value="old('price', $product->price)" step="0.01" min="0" required />
                
                <x-input type="number" name="cost_price" label="Precio de Costo (S/)" :value="old('cost_price', $product->cost_price)" step="0.01" min="0" />
                
                <x-input type="number" name="discount" label="Descuento (%)" :value="old('discount', $product->discount)" min="0" max="100" />
                
                <x-input type="number" name="stock" label="Stock Disponible" :value="old('stock', $product->stock)" min="0" required />
            </div>
        </div>

        {{-- Variants --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Variantes</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tallas Disponibles</label>
                    @php 
                        $currentSizes = is_array($product->sizes_available) ? $product->sizes_available : json_decode($product->sizes_available, true) ?? [];
                        $selectedSizes = old('sizes_available', $currentSizes);
                    @endphp
                    <div class="flex flex-wrap gap-2">
                        @foreach([36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46] as $size)
                            <label class="relative">
                                <input type="checkbox" name="sizes_available[]" value="{{ $size }}" 
                                       {{ in_array($size, $selectedSizes) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <span class="block px-4 py-2 border border-gray-200 rounded-lg cursor-pointer peer-checked:bg-indigo-500 peer-checked:text-white peer-checked:border-indigo-500 hover:border-indigo-300 transition-colors">
                                    {{ $size }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <x-input name="color" label="Color" :value="old('color', $product->color)" />
            </div>
        </div>

        {{-- Current Images --}}
        @if($product->images->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Imágenes Actuales</h2>
                
                <div class="grid grid-cols-4 gap-4">
                    @foreach($product->images as $image)
                        <div class="relative aspect-square bg-gray-100 rounded-xl overflow-hidden group">
                            <img src="{{ asset('storage/' . $image->filename) }}" class="w-full h-full object-cover">
                            
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                @if(!$image->is_main)
                                    <form action="{{ route('admin.products.set-main-image', [$product, $image]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="p-2 bg-white rounded-lg text-indigo-600 hover:bg-indigo-50" title="Establecer como principal">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.products.delete-image', [$product, $image]) }}" method="POST" onsubmit="return confirm('¿Eliminar esta imagen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-white rounded-lg text-red-600 hover:bg-red-50" title="Eliminar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            
                            @if($image->is_main)
                                <span class="absolute top-2 left-2 px-2 py-1 bg-indigo-500 text-white text-xs font-medium rounded-lg">Principal</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Add New Images --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Agregar Nuevas Imágenes</h2>
            
            <label class="block">
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-indigo-400 transition-colors cursor-pointer">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-600 font-medium">Arrastra imágenes o haz clic para seleccionar</p>
                    <p class="text-sm text-gray-400 mt-1">JPG, PNG, WebP hasta 5MB</p>
                    <input type="file" name="images[]" multiple accept="image/*" class="hidden">
                </div>
            </label>
        </div>

        {{-- Options --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Opciones</h2>
            
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-gray-700">Producto activo</span>
                </label>
                
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-gray-700">Producto destacado</span>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.products.index') }}" class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-colors">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">
                Actualizar Producto
            </button>
        </div>
    </form>
</div>
@endsection
