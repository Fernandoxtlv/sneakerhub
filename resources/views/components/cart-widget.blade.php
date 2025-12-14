{{-- Cart Widget Component --}}
<div x-data="{ open: false }" class="relative">
    <a href="{{ route('cart.index') }}"
        class="relative flex items-center text-gray-300 hover:text-white transition-colors p-2">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        @php
            $cartCount = 0;
            if (auth()->check()) {
                $cart = \App\Models\Cart::where('user_id', auth()->id())->first();
            } else {
                $cart = \App\Models\Cart::where('session_id', session()->getId())->first();
            }
            if ($cart) {
                $cartCount = $cart->items->sum('quantity');
            }
        @endphp
        @if($cartCount > 0)
            <span
                class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs font-bold rounded-full flex items-center justify-center animate-pulse">
                {{ $cartCount > 99 ? '99+' : $cartCount }}
            </span>
        @endif
    </a>
</div>