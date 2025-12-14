@extends('layouts.app')

@section('title', 'Pedido Confirmado')

@section('content')
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                {{-- Success Icon --}}
                <div
                    class="w-20 h-20 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-2">¡Pedido Confirmado!</h1>
                <p class="text-gray-600 mb-6">Tu pedido ha sido procesado exitosamente</p>

                <div class="bg-gray-50 rounded-xl p-6 mb-8">
                    <p class="text-sm text-gray-500 mb-2">Número de Orden</p>
                    <p class="text-2xl font-bold text-indigo-600 font-mono">{{ $order->order_number }}</p>
                </div>

                {{-- Order Details --}}
                <div class="text-left space-y-4 mb-8">
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">Estado</span>
                        @if($order->payment_method == 'yape' && $order->payment_status == 'pending')
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-sm font-medium rounded-full">Esperando Pago
                                Yape</span>
                        @else
                            <span
                                class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">{{ ucfirst($order->status) }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">Método de Pago</span>
                        <span
                            class="font-medium text-gray-900">{{ $order->payment_method == 'yape' ? 'Yape' : 'Efectivo' }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">Total</span>
                        <span class="font-bold text-gray-900">S/ {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

                {{-- Yape Instructions --}}
                @if($order->payment_method == 'yape' && $order->payment_status == 'pending')
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-6 mb-8 text-left">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-purple-900">Completa tu pago con Yape</h3>
                        </div>
                        <ol class="list-decimal list-inside space-y-2 text-purple-800">
                            <li>Abre tu aplicación Yape</li>
                            <li>Yapea al número: <strong>999 888 777</strong></li>
                            <li>Monto: <strong>S/ {{ number_format($order->total, 2) }}</strong></li>
                            <li>Incluye como mensaje: <strong>{{ $order->order_number }}</strong></li>
                        </ol>
                        <p class="mt-4 text-sm text-purple-600">Tu pedido será procesado una vez confirmemos el pago.</p>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('orders.show', $order) }}"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all">
                        Ver Detalles del Pedido
                    </a>
                    <a href="{{ route('catalog') }}"
                        class="px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-all">
                        Seguir Comprando
                    </a>
                </div>
            </div>

            {{-- Email Notice --}}
            <p class="text-center text-gray-500 text-sm mt-6">
                Te enviaremos un correo electrónico con los detalles de tu pedido y actualizaciones de estado.
            </p>
        </div>
    </div>
@endsection