@extends('layouts.admin')

@section('title', 'Nuevo Cupón')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.coupons.index') }}"
                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Nuevo Cupón</h1>
                <p class="text-gray-500">Crear un nuevo cupón de descuento</p>
            </div>
        </div>

        <form action="{{ route('admin.coupons.store') }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            @csrf

            <x-input name="code" label="Código del Cupón" :value="old('code')" placeholder="SUMMER20" required
                hint="Código que ingresará el cliente" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-select name="type" label="Tipo de Descuento" :options="['percentage' => 'Porcentaje', 'fixed' => 'Monto Fijo']" :selected="old('type', 'percentage')" required />
                <x-input type="number" name="value" label="Valor" :value="old('value')" step="0.01" min="0" required
                    placeholder="Ej: 20" hint="Porcentaje o monto en soles" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input type="number" name="min_purchase" label="Compra Mínima (S/)" :value="old('min_purchase')"
                    step="0.01" min="0" hint="Dejar vacío para sin mínimo" />
                <x-input type="number" name="max_discount" label="Descuento Máximo (S/)" :value="old('max_discount')"
                    step="0.01" min="0" hint="Solo aplica para porcentaje" />
            </div>

            <x-input type="number" name="usage_limit" label="Límite de Usos" :value="old('usage_limit')" min="1"
                hint="Dejar vacío para usos ilimitados" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input type="date" name="starts_at" label="Fecha de Inicio" :value="old('starts_at')"
                    hint="Dejar vacío para activar inmediatamente" />
                <x-input type="date" name="expires_at" label="Fecha de Expiración" :value="old('expires_at')"
                    hint="Dejar vacío para sin expiración" />
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" id="is_active">
                <label for="is_active" class="text-gray-700">Cupón activo</label>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.coupons.index') }}"
                    class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-colors">Cancelar</a>
                <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">Crear
                    Cupón</button>
            </div>
        </form>
    </div>
@endsection