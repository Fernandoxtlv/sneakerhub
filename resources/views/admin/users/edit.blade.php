@extends('layouts.admin')

@section('title', 'Editar: ' . $user->name)

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}"
                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Usuario</h1>
                <p class="text-gray-500">{{ $user->name }}</p>
            </div>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
            @csrf
            @method('PUT')

            <x-input name="name" label="Nombre" :value="old('name', $user->name)" required />
            <x-input type="email" name="email" label="Email" :value="old('email', $user->email)" required />
            <x-input name="phone" label="Teléfono" :value="old('phone', $user->phone)" />

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <textarea name="address" rows="2"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 bg-gray-50 hover:bg-white focus:bg-white transition-all">{{ old('address', $user->address) }}</textarea>
            </div>

            <x-select name="role" label="Rol" :options="$roles->pluck('name', 'name')->mapWithKeys(fn($v, $k) => [$k => ucfirst($v)])" :selected="old('role', $user->roles->first()?->name ?? 'client')" required />

            <div class="pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-500 mb-4">Deja los campos de contraseña vacíos para mantener la actual</p>
                <div class="space-y-4">
                    <x-input type="password" name="password" label="Nueva Contraseña" />
                    <x-input type="password" name="password_confirmation" label="Confirmar Contraseña" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}"
                    class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-colors">Cancelar</a>
                <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">Actualizar</button>
            </div>
        </form>
    </div>
@endsection