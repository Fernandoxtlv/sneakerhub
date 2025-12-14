@extends('layouts.app')

@section('title', 'Pedido ' . $order->order_number)

@section('content')
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('orders.index') }}"
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Status Timeline --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Estado del Pedido</h2>

                        @php
                            $statuses = ['pending', 'paid', 'processing', 'shipped', 'completed'];
                            $currentIndex = array_search($order->status, $statuses);
                        @endphp

                        <div class="flex items-center justify-between">
                            @foreach($statuses as $index => $status)
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center {{ $index <= $currentIndex ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' }}">
                                        @if($index <= $currentIndex)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span class="text-sm font-medium">{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 text-center">{{ ucfirst($status) }}</p>
                                </div>
                                @if($index < count($statuses) - 1)
                                    <div class="flex-1 h-1 {{ $index < $currentIndex ? 'bg-green-500' : 'bg-gray-200' }} mx-2">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Items --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Productos</h2>

                        <div class="space-y-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($item->product && $item->product->mainImage)
                                            <img src="{{ asset('storage/' . $item->product->mainImage->path) }}"
                                                class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $item->name }}</p>
                                        <p class="text-sm text-gray-500">
                                            SKU: {{ $item->sku }}
                                            @if($item->size) | Talla: {{ $item->size }} @endif
                                        </p>
                                        <p class="text-sm text-gray-600 mt-1">{{ $item->quantity }} x S/
                                            {{ number_format($item->price, 2) }}
                                        </p>
                                    </div>
                                    <p class="text-lg font-bold text-indigo-600">S/ {{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Summary --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumen</h2>

                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Subtotal</dt>
                                <dd class="font-medium">S/ {{ number_format($order->subtotal, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Impuesto</dt>
                                <dd class="font-medium">S/ {{ number_format($order->tax, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Envío</dt>
                                <dd class="font-medium">S/ {{ number_format($order->delivery_fee, 2) }}</dd>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-gray-100">
                                <dt class="text-lg font-bold">Total</dt>
                                <dd class="text-lg font-bold text-indigo-600">S/ {{ number_format($order->total, 2) }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Payment --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pago</h2>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Método</span>
                                <span
                                    class="font-medium">{{ $order->payment_method == 'yape' ? 'Yape' : 'Efectivo' }}</span>
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

                    {{-- Shipping --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Envío</h2>

                        @php
                            $shipping = is_array($order->shipping_address) ? $order->shipping_address : json_decode($order->shipping_address, true) ?? [];
                        @endphp

                        <div class="space-y-2 text-gray-600">
                            <p class="font-medium text-gray-900">{{ $shipping['name'] ?? '' }}</p>
                            <p>{{ $shipping['address'] ?? '' }}</p>
                            <p>{{ $shipping['city'] ?? '' }}</p>
                            <p>{{ $shipping['phone'] ?? '' }}</p>
                        </div>
                    </div>

                    {{-- Download Invoice --}}
                    @if($order->invoice)
                        <a href="{{ route('orders.download-boleta', $order) }}"
                            class="block w-full py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-center font-medium rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all">
                            Descargar Boleta
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection