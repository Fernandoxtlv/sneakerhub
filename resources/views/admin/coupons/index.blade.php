@extends('layouts.admin')

@section('title', 'Cupones')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Cupones</h1>
                <p class="text-gray-500">Gestiona los cupones de descuento</p>
            </div>
            <a href="{{ route('admin.coupons.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Cupón
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Código</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Valor</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Usos</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Vigencia</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($coupons as $coupon)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-mono font-bold text-indigo-600">{{ $coupon->code }}</td>
                                <td class="px-6 py-4">
                                    @if($coupon->type == 'percentage')
                                        <span
                                            class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">Porcentaje</span>
                                    @else
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Monto
                                            Fijo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium">
                                    {{ $coupon->type == 'percentage' ? $coupon->value . '%' : 'S/ ' . number_format($coupon->value, 2) }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $coupon->used_count ?? 0 }} / {{ $coupon->usage_limit ?? '∞' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 text-sm">
                                    @if($coupon->starts_at && $coupon->expires_at)
                                        {{ $coupon->starts_at->format('d/m/Y') }} - {{ $coupon->expires_at->format('d/m/Y') }}
                                    @elseif($coupon->expires_at)
                                        Hasta {{ $coupon->expires_at->format('d/m/Y') }}
                                    @else
                                        Sin límite
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($coupon->is_active && (!$coupon->expires_at || $coupon->expires_at->isFuture()))
                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Activo</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                            class="p-2 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar este cupón?')">
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="text-gray-500">No hay cupones</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection