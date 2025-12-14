<x-layouts.app title="Carrito de Compras">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Carrito de Compras</h1>

        @if($cart->is_empty)
            <div class="text-center py-16">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Tu carrito está vacío</h2>
                <p class="text-gray-500 mb-6">¡Explora nuestro catálogo y encuentra tus zapatillas favoritas!</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary">Ver Catálogo</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cart->items as $item)
                        <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col sm:flex-row gap-6">
                            <!-- Product Image -->
                            <div class="w-full sm:w-32 h-32 shrink-0 bg-gray-100 rounded-xl overflow-hidden">
                                @if($item->product->mainImage)
                                    <img src="{{ Storage::url($item->product->mainImage->path_thumb ?? $item->product->mainImage->path) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <a href="{{ route('product.show', $item->product) }}" class="font-semibold text-gray-900 hover:text-primary-600">
                                            {{ $item->product->name }}
                                        </a>
                                        <p class="text-sm text-gray-500 mt-1">{{ $item->product->brand->name }}</p>
                                        @if($item->size)
                                            <p class="text-sm text-gray-500">Talla: {{ $item->size }}</p>
                                        @endif
                                    </div>
                                    <form action="{{ route('cart.remove', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    <!-- Quantity -->
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2" x-data="{ qty: {{ $item->quantity }} }">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" @click="qty = Math.max(1, qty - 1)" class="qty-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <input type="number" name="quantity" x-model="qty" min="1" max="{{ $item->product->stock }}" class="w-16 text-center form-input py-2 text-sm">
                                        <button type="button" @click="qty = Math.min({{ $item->product->stock }}, qty + 1)" class="qty-btn">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                        <button type="submit" class="ml-2 text-sm text-primary-600 hover:text-primary-700 font-medium">Actualizar</button>
                                    </form>

                                    <!-- Price -->
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-gray-900">{{ $item->formatted_subtotal }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->formatted_price }} x {{ $item->quantity }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                        <h2 class="text-lg font-bold text-gray-900 mb-6">Resumen del Pedido</h2>

                        <!-- Coupon -->
                        <div class="mb-6">
                            @if($cart->coupon_code)
                                <div class="flex items-center justify-between bg-green-50 rounded-xl p-4 border border-green-200">
                                    <div>
                                        <p class="font-medium text-green-700">{{ $cart->coupon_code }}</p>
                                        <p class="text-sm text-green-600">-{{ config('sneakerhub.currency.symbol') }} {{ number_format($cart->discount_amount, 2) }}</p>
                                    </div>
                                    <form action="{{ route('cart.remove-coupon') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-green-600 hover:text-green-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('cart.apply-coupon') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="coupon_code" placeholder="Código de cupón" class="form-input flex-1">
                                    <button type="submit" class="btn btn-outline btn-sm">Aplicar</button>
                                </form>
                            @endif
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Subtotal ({{ $cart->items_count }} productos)</span>
                                <span class="font-medium text-gray-900">{{ $cart->formatted_subtotal }}</span>
                            </div>
                            @if($cart->discount_amount > 0)
                                <div class="flex justify-between text-green-600">
                                    <span>Descuento</span>
                                    <span>-{{ config('sneakerhub.currency.symbol') }} {{ number_format($cart->discount_amount, 2) }}</span>
                                </div>
                            @endif
                        </div>

                        <hr class="my-4">

                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-900">Total</span>
                            <span class="text-gray-900">{{ $cart->formatted_total }}</span>
                        </div>

                        <p class="text-xs text-gray-500 mt-2">* Impuestos y envío se calcularán en el checkout</p>

                        <a href="{{ route('checkout') }}" class="btn btn-primary w-full mt-6">
                            Proceder al Checkout
                        </a>

                        <a href="{{ route('catalog') }}" class="btn btn-ghost w-full mt-2">
                            Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
