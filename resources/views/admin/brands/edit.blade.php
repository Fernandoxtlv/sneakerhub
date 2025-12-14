@extends('layouts.admin')

@section('title', 'Editar: ' . $brand->name)

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.brands.index') }}"
                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Marca</h1>
                <p class="text-gray-500">{{ $brand->name }}</p>
            </div>
        </div>

        <form action="{{ route('admin.brands.update', $brand) }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            @csrf
            @method('PUT')

            <x-input name="name" label="Nombre" :value="old('name', $brand->name)" required />
            <x-input name="slug" label="Slug" :value="old('slug', $brand->slug)" />

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.brands.index') }}"
                    class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-colors">Cancelar</a>
                <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">Actualizar</button>
            </div>
        </form>
    </div>
@endsection