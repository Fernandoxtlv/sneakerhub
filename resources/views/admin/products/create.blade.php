@extends('layouts.admin')

@section('title', 'Nuevo Producto')

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
            <h1 class="text-2xl font-bold text-gray-900">Nuevo Producto</h1>
            <p class="text-gray-500">Añade una nueva zapatilla al catálogo</p>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Información Básica</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-input name="name" label="Nombre del Producto" :value="old('name')" placeholder="Ej: Nike Air Max 90" required />
                </div>
                
                <x-input name="sku" label="SKU" :value="old('sku')" placeholder="Ej: NAM90-001" required />
                
                <x-input name="slug" label="Slug (URL)" :value="old('slug')" placeholder="Se genera automáticamente" hint="Dejar vacío para generar automáticamente" />
                
                <x-select name="brand_id" label="Marca" :options="$brands->pluck('name', 'id')" :selected="old('brand_id')" required />
                
                <x-select name="category_id" label="Categoría" :options="$categories->pluck('name', 'id')" :selected="old('category_id')" required />
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="description" rows="4" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 bg-gray-50 hover:bg-white focus:bg-white transition-all"
                          placeholder="Describe el producto...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Pricing & Stock --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Precio y Stock</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-input type="number" name="price" label="Precio de Venta (S/)" :value="old('price')" placeholder="0.00" step="0.01" min="0" required />
                
                <x-input type="number" name="cost_price" label="Precio de Costo (S/)" :value="old('cost_price')" placeholder="0.00" step="0.01" min="0" />
                
                <x-input type="number" name="discount" label="Descuento (%)" :value="old('discount', 0)" placeholder="0" min="0" max="100" />
                
                <x-input type="number" name="stock" label="Stock Disponible" :value="old('stock', 0)" placeholder="0" min="0" required />
            </div>
        </div>

        {{-- Variants --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Variantes</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tallas Disponibles</label>
                    <div class="flex flex-wrap gap-2">
                        @php $selectedSizes = old('sizes_available', []); @endphp
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
                
                <x-input name="color" label="Color" :value="old('color')" placeholder="Ej: Negro, Blanco, Rojo" />
            </div>
        </div>

        {{-- Images --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Imágenes</h2>
            
            <div x-data="{ files: [] }">
                <label class="block">
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-indigo-400 transition-colors cursor-pointer"
                         ondragover="event.preventDefault()" 
                         ondrop="event.preventDefault(); this.querySelector('input').files = event.dataTransfer.files;">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-gray-600 font-medium">Arrastra imágenes o haz clic para seleccionar</p>
                        <p class="text-sm text-gray-400 mt-1">JPG, PNG, WebP hasta 5MB</p>
                        <input type="file" name="images[]" multiple accept="image/*" class="hidden" 
                               @change="files = Array.from($event.target.files)">
                    </div>
                </label>
                
                <template x-if="files.length > 0">
                    <div class="mt-4 grid grid-cols-4 gap-4">
                        <template x-for="(file, index) in files" :key="index">
                            <div class="relative aspect-square bg-gray-100 rounded-xl overflow-hidden">
                                <img :src="URL.createObjectURL(file)" class="w-full h-full object-cover">
                                <button type="button" @click="files.splice(index, 1)" 
                                        class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- Options --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Opciones</h2>
            
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-gray-700">Producto activo</span>
                </label>
                
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}
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
                Crear Producto
            </button>
        </div>
    </form>
</div>
@endsection
