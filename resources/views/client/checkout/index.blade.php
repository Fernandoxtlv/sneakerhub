@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

            <form action="{{ route('checkout.process') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @csrf

                {{-- Shipping Information --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Información de Envío</h2>

                        <div class="space-y-4">
                            <x-input name="shipping_name" label="Nombre Completo" :value="old('shipping_name', auth()->user()->name)" required />

                            <x-input name="shipping_phone" label="Teléfono" :value="old('shipping_phone', auth()->user()->phone)" required placeholder="999 888 777" />

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección de Envío <span
                                        class="text-red-500">*</span></label>
                                <textarea name="shipping_address" rows="3" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 bg-gray-50 hover:bg-white focus:bg-white transition-all"
                                    placeholder="Calle, número, referencia...">{{ old('shipping_address', auth()->user()->address) }}</textarea>
                                @error('shipping_address')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <x-input name="shipping_city" label="Ciudad" :value="old('shipping_city', 'Lima')" required />
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Método de Pago</h2>

                        <div x-data="{ method: '{{ old('payment_method', 'cash') }}' }" class="space-y-4">
                            {{-- Cash Option --}}
                            <label class="flex items-start gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all"
                                :class="method === 'cash' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="payment_method" value="cash" x-model="method"
                                    class="mt-1 w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Pago en Efectivo</p>
                                            <p class="text-sm text-gray-500">Paga al recibir tu pedido</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            {{-- Yape Option --}}
                            <label class="flex items-start gap-4 p-4 border-2 rounded-xl cursor-pointer transition-all"
                                :class="method === 'yape' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="payment_method" value="yape" x-model="method"
                                    class="mt-1 w-5 h-5 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">Yape</p>
                                            <p class="text-sm text-gray-500">Paga con tu billetera digital</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        @error('payment_method')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Notas del Pedido</h2>
                        <textarea name="notes" rows="3"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 bg-gray-50 hover:bg-white focus:bg-white transition-all"
                            placeholder="Instrucciones especiales de entrega, horarios preferidos, etc.">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Resumen del Pedido</h2>

                        {{-- Items --}}
                        <div class="space-y-4 mb-6">
                            @foreach($cart->items as $item)
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($item->product->mainImage)
                                            <img src="{{ asset('storage/' . $item->product->mainImage->path) }}"
                                                class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-900 truncate">{{ $item->product->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->quantity }} x S/
                                            {{ number_format($item->price, 2) }}
                                        </p>
                                    </div>
                                    <p class="font-semibold text-gray-900">S/ {{ number_format($item->subtotal, 2) }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-100 pt-4 space-y-3">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>S/ {{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Impuesto (18%)</span>
                                <span>S/ {{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Envío</span>
                                <span>S/ {{ number_format($deliveryFee, 2) }}</span>
                            </div>
                            @if(isset($discount) && $discount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Descuento</span>
                                    <span>-S/ {{ number_format($discount, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-xl font-bold text-gray-900 pt-3 border-t border-gray-100">
                                <span>Total</span>
                                <span>S/ {{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full mt-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 transform hover:scale-[1.02] transition-all duration-300 shadow-lg shadow-indigo-500/30">
                            Confirmar Pedido
                        </button>

                        <p class="text-center text-sm text-gray-500 mt-4">
                            Al confirmar, aceptas nuestros términos y condiciones
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection