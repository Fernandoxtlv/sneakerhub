@extends('layouts.admin')

@section('title', 'Pagos')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pagos</h1>
                <p class="text-gray-500">Historial de transacciones de pago</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por referencia..."
                    class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">

                <select name="method"
                    class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50">
                    <option value="">Todos los métodos</option>
                    <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Efectivo</option>
                    <option value="yape" {{ request('method') == 'yape' ? 'selected' : '' }}>Yape</option>
                </select>

                <select name="status"
                    class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50">
                    <option value="">Todos los estados</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Fallido</option>
                </select>

                <button type="submit"
                    class="px-4 py-2.5 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-colors font-medium">Filtrar</button>
            </form>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500 mb-1">Total Recaudado</p>
                <p class="text-3xl font-bold text-gray-900">S/ {{ number_format($stats['total'] ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500 mb-1">Pagos Efectivo</p>
                <p class="text-3xl font-bold text-green-600">S/ {{ number_format($stats['cash'] ?? 0, 2) }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500 mb-1">Pagos Yape</p>
                <p class="text-3xl font-bold text-purple-600">S/ {{ number_format($stats['yape'] ?? 0, 2) }}</p>
            </div>
        </div>

        {{-- Payments Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Referencia</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Orden</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Método</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Monto</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-mono text-sm text-gray-600">{{ $payment->reference ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.orders.show', $payment->order) }}"
                                        class="text-indigo-600 hover:text-indigo-700 font-medium">
                                        {{ $payment->order->order_number ?? '-' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $payment->order->user->name ?? 'Invitado' }}</td>
                                <td class="px-6 py-4">
                                    @if($payment->payment_method == 'yape')
                                        <span
                                            class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">Yape</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Efectivo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900">S/ {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment->status == 'completed')
                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Completado</span>
                                    @elseif($payment->status == 'failed')
                                        <span
                                            class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">Fallido</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-amber-100 text-amber-700 text-sm font-medium rounded-full">Pendiente</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="text-gray-500">No hay pagos registrados</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection