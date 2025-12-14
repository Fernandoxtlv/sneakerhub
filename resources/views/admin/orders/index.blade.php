@extends('layouts.admin')

@section('title', 'Pedidos')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pedidos</h1>
            <p class="text-gray-500">Gestiona los pedidos de la tienda</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por # de orden..." 
                   class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
            
            <select name="status" class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50">
                <option value="">Todos los estados</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pagado</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Procesando</option>
                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Enviado</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
            </select>
            
            <select name="payment_method" class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50">
                <option value="">Todos los métodos</option>
                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Efectivo</option>
                <option value="yape" {{ request('payment_method') == 'yape' ? 'selected' : '' }}>Yape</option>
            </select>
            
            <button type="submit" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-colors font-medium">Filtrar</button>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Orden</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Método</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Pago</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono font-semibold text-gray-900">{{ $order->order_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $order->user->name ?? 'Invitado' }}</p>
                                <p class="text-sm text-gray-500">{{ $order->user->email ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900">S/ {{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($order->payment_method == 'yape')
                                    <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">Yape</span>
                                @else
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Efectivo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'paid' => 'bg-blue-100 text-blue-700',
                                        'processing' => 'bg-indigo-100 text-indigo-700',
                                        'shipped' => 'bg-cyan-100 text-cyan-700',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        'refunded' => 'bg-gray-100 text-gray-700',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pendiente',
                                        'paid' => 'Pagado',
                                        'processing' => 'Procesando',
                                        'shipped' => 'Enviado',
                                        'completed' => 'Completado',
                                        'cancelled' => 'Cancelado',
                                        'refunded' => 'Reembolsado',
                                    ];
                                @endphp
                                <span class="px-3 py-1 {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }} text-sm font-medium rounded-full">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($order->payment_status == 'paid')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Pagado</span>
                                @elseif($order->payment_status == 'failed')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">Fallido</span>
                                @else
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-sm font-medium rounded-full">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    @if($order->invoice)
                                        <a href="{{ route('admin.orders.download-invoice', $order) }}" class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <p class="text-gray-500">No hay pedidos</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
