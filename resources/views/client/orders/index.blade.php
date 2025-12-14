@extends('layouts.app')

@section('title', 'Mis Pedidos')

@section('content')
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Mis Pedidos</h1>

            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="p-6">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <div>
                                        <p class="text-lg font-bold font-mono text-indigo-600">{{ $order->order_number }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 text-amber-700',
                                                'paid' => 'bg-blue-100 text-blue-700',
                                                'processing' => 'bg-indigo-100 text-indigo-700',
                                                'shipped' => 'bg-cyan-100 text-cyan-700',
                                                'completed' => 'bg-green-100 text-green-700',
                                                'cancelled' => 'bg-red-100 text-red-700',
                                            ];
                                            $statusLabels = [
                                                'pending' => 'Pendiente',
                                                'paid' => 'Pagado',
                                                'processing' => 'Procesando',
                                                'shipped' => 'Enviado',
                                                'completed' => 'Completado',
                                                'cancelled' => 'Cancelado',
                                            ];
                                        @endphp
                                        <span
                                            class="px-4 py-1.5 {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }} text-sm font-medium rounded-full">
                                            {{ $statusLabels[$order->status] ?? $order->status }}
                                        </span>
                                        <p class="text-xl font-bold text-gray-900">S/ {{ number_format($order->total, 2) }}</p>
                                    </div>
                                </div>

                                {{-- Items Preview --}}
                                <div class="mt-4 flex items-center gap-2 overflow-x-auto">
                                    @foreach($order->items->take(4) as $item)
                                        <div class="w-14 h-14 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                            @if($item->product && $item->product->mainImage)
                                                <img src="{{ asset('storage/' . $order->items->first()->product->mainImage->path) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($order->items->count() > 4)
                                        <div
                                            class="w-14 h-14 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500 font-medium flex-shrink-0">
                                            +{{ $order->items->count() - 4 }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="mt-6 flex items-center gap-4">
                                    <a href="{{ route('orders.show', $order) }}"
                                        class="text-indigo-600 hover:text-indigo-700 font-medium">
                                        Ver Detalles →
                                    </a>
                                    @if($order->invoice)
                                        <a href="{{ route('orders.download-boleta', $order) }}"
                                            class="text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Descargar Boleta
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($orders->hasPages())
                    <div class="mt-8">
                        {{ $orders->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">No tienes pedidos aún</h2>
                    <p class="text-gray-500 mb-6">¡Explora nuestro catálogo y encuentra las zapatillas perfectas!</p>
                    <a href="{{ route('catalog') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all">
                        Explorar Catálogo
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection