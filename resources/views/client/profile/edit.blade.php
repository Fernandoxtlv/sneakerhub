@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Mi Perfil</h1>

            {{-- Profile Form --}}
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Información Personal</h2>

                    <div class="flex items-center gap-6 mb-6">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-gray-500">{{ auth()->user()->email }}</p>
                            <p class="text-sm text-gray-400">Miembro desde {{ auth()->user()->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>

                    <x-input name="name" label="Nombre Completo" :value="old('name', auth()->user()->name)" required />
                    <x-input type="email" name="email" label="Correo Electrónico" :value="old('email', auth()->user()->email)" required />
                    <x-input name="phone" label="Teléfono" :value="old('phone', auth()->user()->phone)"
                        placeholder="999 888 777" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <textarea name="address" rows="3"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 bg-gray-50 hover:bg-white focus:bg-white transition-all"
                            placeholder="Tu dirección de envío predeterminada">{{ old('address', auth()->user()->address) }}</textarea>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                    <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Cambiar Contraseña</h2>
                    <p class="text-sm text-gray-500">Deja estos campos vacíos si no deseas cambiar tu contraseña.</p>

                    <x-input type="password" name="current_password" label="Contraseña Actual" />
                    <x-input type="password" name="password" label="Nueva Contraseña" />
                    <x-input type="password" name="password_confirmation" label="Confirmar Nueva Contraseña" />
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">
                        Guardar Cambios
                    </button>
                </div>
            </form>

            {{-- Delete Account --}}
            <div class="mt-12 bg-red-50 rounded-2xl border border-red-200 p-6">
                <h2 class="text-lg font-semibold text-red-900 mb-2">Eliminar Cuenta</h2>
                <p class="text-red-700 text-sm mb-4">Una vez que elimines tu cuenta, todos tus datos serán eliminados
                    permanentemente.</p>

                <form action="{{ route('profile.destroy') }}" method="POST"
                    onsubmit="return confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-6 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-colors">
                        Eliminar mi cuenta
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection