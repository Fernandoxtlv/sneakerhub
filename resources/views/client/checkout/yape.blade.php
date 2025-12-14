@extends('layouts.app')

@section('title', 'Pagar con Yape')

@section('content')
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Paga con Yape</h1>
                    <p class="text-gray-500 mb-8">Escanea el código QR o usa el número para pagar</p>

                    <div class="bg-purple-50 rounded-xl p-8 max-w-md mx-auto border-2 border-purple-100 border-dashed">
                        <div class="mb-6">
                            <p class="text-sm font-semibold text-purple-800 uppercase tracking-wider mb-2">Monto a Pagar</p>
                            <p class="text-4xl font-bold text-purple-900">S/ {{ number_format($yapeData['amount'], 2) }}</p>
                        </div>

                        {{-- QR Placeholder (In real app, generate real QR) --}}
                        <div class="bg-white p-4 rounded-xl shadow-sm inline-block mb-6">
                            {{-- Using a placeholder image or a generated QR --}}
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=example-yape-data"
                                alt="Yape QR" class="w-48 h-48">
                        </div>

                        <div class="space-y-2 text-left bg-white p-4 rounded-lg shadow-sm border border-purple-100">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Número:</span>
                                <span class="font-bold text-gray-900">{{ $yapeData['phone'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Beneficiario:</span>
                                <span class="font-bold text-gray-900">{{ $yapeData['merchant_name'] }}</span>
                            </div>
                            <div class="flex justify-between border-t border-gray-100 pt-2 mt-2">
                                <span class="text-gray-500">CODIGO REFERENCIA:</span>
                                <span class="font-bold text-purple-600 tracking-wider">{{ $yapeData['reference'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 space-y-4 text-left max-w-md mx-auto">
                        <h3 class="font-semibold text-gray-900">Instrucciones:</h3>
                        <ul class="space-y-3">
                            @foreach($yapeData['instructions'] as $instruction)
                                <li class="flex items-start gap-3">
                                    <span
                                        class="bg-purple-100 text-purple-600 w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">
                                        {{ $loop->iteration }}
                                    </span>
                                    <span class="text-gray-600 text-sm">{{ $instruction }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-8 border-t border-gray-100 pt-8">
                        <a href="{{ route('orders.show', $order) }}"
                            class="inline-flex items-center justify-center px-8 py-3 bg-gray-900 text-white font-semibold rounded-xl hover:bg-gray-800 transition-colors">
                            Ya realicé el pago
                        </a>
                        <p class="text-xs text-gray-400 mt-4">
                            Tu pedido #{{ $order->id }} está pendiente de validación.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection