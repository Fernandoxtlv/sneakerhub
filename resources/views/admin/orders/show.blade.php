@extends('layouts.admin')

@section('title', 'Pedido ' . $order->order_number)

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.orders.index') }}"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Pedido {{ $order->order_number }}</h1>
                    <p class="text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($order->invoice)
                    <a href="{{ route('admin.orders.download-invoice', $order) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 text-white font-medium rounded-xl hover:bg-green-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Descargar Boleta
                    </a>
                @else
                    <form action="{{ route('admin.orders.generate-invoice', $order) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-500 text-white font-medium rounded-xl hover:bg-indigo-600 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Generar Boleta
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Order Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Status Update --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Estado del Pedido</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST"
                            class="flex items-end gap-3">
                            @csrf
                            @method('PATCH')
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select name="status"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendiente
                                    </option>
                                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Pagado</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                        Procesando</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Enviado
                                    </option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completado
                                    </option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelado
                                    </option>
                                </select>
                            </div>
                            <button type="submit"
                                class="px-5 py-2.5 bg-indigo-500 text-white font-medium rounded-xl hover:bg-indigo-600 transition-colors">Actualizar</button>
                        </form>

                        <form action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST"
                            class="flex items-end gap-3">
                            @csrf
                            @method('PATCH')
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado de Pago</label>
                                <select name="payment_status"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50">
                                    <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>
                                        Pendiente</option>
                                    <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Pagado
                                    </option>
                                    <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Fallido
                                    </option>
                                </select>
                            </div>
                            <button type="submit"
                                class="px-5 py-2.5 bg-green-500 text-white font-medium rounded-xl hover:bg-green-600 transition-colors">Actualizar</button>
                        </form>
                    </div>
                </div>

                {{-- Items --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Productos</h2>

                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($item->product && $item->product->mainImage)
                                        <img src="{{ asset('storage/' . $item->product->mainImage->path) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $item->name }}</p>
                                    <p class="text-sm text-gray-500">SKU: {{ $item->sku }} @if($item->size) | Talla:
                                    {{ $item->size }} @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900">S/ {{ number_format($item->price, 2) }} x
                                        {{ $item->quantity }}
                                    </p>
                                    <p class="text-lg font-bold text-indigo-600">S/ {{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Order Summary --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumen</h2>

                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Subtotal</dt>
                            <dd class="font-medium text-gray-900">S/ {{ number_format($order->subtotal, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Impuesto</dt>
                            <dd class="font-medium text-gray-900">S/ {{ number_format($order->tax, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Envío</dt>
                            <dd class="font-medium text-gray-900">S/ {{ number_format($order->delivery_fee, 2) }}</dd>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-100">
                            <dt class="text-lg font-bold text-gray-900">Total</dt>
                            <dd class="text-lg font-bold text-indigo-600">S/ {{ number_format($order->total, 2) }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Customer Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Cliente</h2>

                    <div class="space-y-3">
                        <p class="font-medium text-gray-900">{{ $order->user->name ?? 'Invitado' }}</p>
                        <p class="text-gray-600">{{ $order->user->email ?? '-' }}</p>
                        <p class="text-gray-600">{{ $order->user->phone ?? '-' }}</p>
                    </div>
                </div>

                {{-- Shipping Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Dirección de Envío</h2>

                    @php
                        $shipping = is_array($order->shipping_address) ? $order->shipping_address : json_decode($order->shipping_address, true) ?? [];
                    @endphp

                    <div class="space-y-2 text-gray-600">
                        <p class="font-medium text-gray-900">{{ $shipping['name'] ?? '-' }}</p>
                        <p>{{ $shipping['address'] ?? '-' }}</p>
                        <p>{{ $shipping['city'] ?? '-' }}</p>
                        <p>{{ $shipping['phone'] ?? '-' }}</p>
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Pago</h2>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Método</span>
                            <span
                                class="font-medium text-gray-900">{{ $order->payment_method == 'yape' ? 'Yape' : 'Efectivo' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Estado</span>
                            @if($order->payment_status == 'paid')
                                <span
                                    class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Pagado</span>
                            @else
                                <span
                                    class="px-3 py-1 bg-amber-100 text-amber-700 text-sm font-medium rounded-full">Pendiente</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                @if($order->notes)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Notas</h2>
                        <p class="text-gray-600">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection