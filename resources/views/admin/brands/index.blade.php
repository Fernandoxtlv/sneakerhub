@extends('layouts.admin')

@section('title', 'Marcas')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Marcas</h1>
                <p class="text-gray-500">Gestiona las marcas de productos</p>
            </div>
            <button x-data x-on:click="$dispatch('open-modal-create-brand')"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Marca
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($brands as $brand)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-xl font-bold">
                            {{ substr($brand->name, 0, 2) }}
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.brands.edit', $brand) }}"
                                class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST"
                                onsubmit="return confirm('¿Eliminar esta marca?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $brand->name }}</h3>
                    <p class="text-sm text-gray-500 font-mono mb-3">/{{ $brand->slug }}</p>
                    <div class="pt-4 border-t border-gray-100">
                        <span class="text-sm text-gray-600">{{ $brand->products_count ?? $brand->products->count() }}
                            productos</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500">No hay marcas registradas</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Create Modal --}}
    <div x-data="{ open: false }" x-on:open-modal-create-brand.window="open = true">
        <div x-show="open" x-transition
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
            style="display: none;">
            <div @click.outside="open = false" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
                <form action="{{ route('admin.brands.store') }}" method="POST">
                    @csrf
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900">Nueva Marca</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <x-input name="name" label="Nombre de la Marca" required />
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                        <button type="button" @click="open = false"
                            class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium">Cancelar</button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection