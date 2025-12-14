<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SneakerHub' }} - {{ config('app.name', 'SneakerHub') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|outfit:400,500,600,700,800"
        rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50" x-data="cart">
    <!-- Top Bar -->
    <div class="bg-sneaker-dark text-white text-sm py-2">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <span class="hidden sm:inline">🚚 Envío gratis en compras mayores a S/ 300</span>
            <div class="flex items-center gap-4">
                <a href="tel:+51999999999" class="hover:text-primary-400 transition-colors">📞 +51 999 999 999</a>
                <span class="hidden sm:inline">|</span>
                <a href="mailto:tienda@sneakerhub.com"
                    class="hidden sm:inline hover:text-primary-400 transition-colors">tienda@sneakerhub.com</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="SneakerHub" class="h-16 w-auto">
                </a>

                <!-- Search Bar (Desktop) -->
                <div class="hidden md:flex flex-1 max-w-xl mx-8" x-data="search">
                    <div class="relative w-full">
                        <input type="text" x-model="query" @input.debounce.300ms="search"
                            @focus="showResults = query.length >= 2" @click.away="showResults = false"
                            placeholder="Buscar zapatillas..."
                            class="w-full pl-12 pr-4 py-3 bg-gray-100 rounded-xl border-0 focus:ring-2 focus:ring-primary-500 focus:bg-white transition-all">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>

                        <!-- Search Results Dropdown -->
                        <div x-show="showResults && results.length > 0" x-transition
                            class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-xl border border-gray-200 max-h-96 overflow-y-auto z-50">
                            <template x-for="product in results" :key="product.id">
                                <a :href="`/product/${product.slug}`"
                                    class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors">
                                    <img :src="product.thumbnail_url" :alt="product.name"
                                        class="w-16 h-16 rounded-lg object-cover">
                                    <div>
                                        <div class="font-semibold text-gray-900" x-text="product.name"></div>
                                        <div class="text-primary-600 font-bold" x-text="product.formatted_price"></div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">
                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}"
                        class="relative p-2 hover:bg-gray-100 rounded-xl transition-colors group">
                        <svg class="w-6 h-6 text-gray-700 group-hover:text-primary-600 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span x-show="count > 0" x-text="count"
                            class="absolute -top-1 -right-1 w-5 h-5 bg-primary-600 text-white text-xs flex items-center justify-center rounded-full animate-scale-in">
                        </span>
                    </a>

                    <!-- User Menu -->
                    @auth
                        <div class="relative" x-data="dropdown">
                            <button @click="toggle"
                                class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-xl transition-colors">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-semibold">{{ auth()->user()->initials }}</span>
                                </div>
                                <span class="hidden lg:inline font-medium text-gray-700">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" @click.away="close" x-transition
                                class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50">
                                @if(auth()->user()->hasStaffAccess())
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                        <span>Panel Admin</span>
                                    </a>
                                    <hr class="my-2">
                                @endif
                                <a href="{{ route('orders.index') }}"
                                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>Mis Pedidos</span>
                                </a>
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>Mi Perfil</span>
                                </a>
                                <hr class="my-2">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition-colors w-full text-left text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        <span>Cerrar Sesión</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex btn btn-ghost btn-sm">Iniciar Sesión</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Registrarse</a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Categories Navigation -->
        <nav class="border-t border-gray-100">
            <div class="container mx-auto px-4">
                <ul class="flex items-center gap-8 overflow-x-auto py-4 text-sm font-medium scrollbar-hide">
                    <li><a href="{{ route('catalog') }}"
                            class="whitespace-nowrap hover:text-primary-600 transition-colors {{ request()->routeIs('catalog') ? 'text-primary-600' : 'text-gray-600' }}">Todos</a>
                    </li>
                    @foreach(\App\Models\Category::active()->ordered()->limit(6)->get() as $category)
                        <li>
                            <a href="{{ route('category.show', $category) }}"
                                class="whitespace-nowrap hover:text-primary-600 transition-colors {{ request()->is('category/' . $category->slug) ? 'text-primary-600' : 'text-gray-600' }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </nav>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="toast toast-success animate-slide-up" x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 4000)">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="toast toast-error animate-slide-up" x-data="{ show: true }" x-show="show"
            x-init="setTimeout(() => show = false, 4000)">
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-sneaker-dark text-white mt-20">
        <div class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <!-- Brand -->
                <div>
                    <div class="flex items-center gap-2 mb-6">
                        <img src="{{ asset('images/logo.png') }}" alt="SneakerHub" class="h-16 w-auto">
                    </div>
                    <p class="text-gray-400 mb-6">Tu destino premium para las mejores zapatillas del mercado.</p>
                    <div class="flex gap-4">
                        <a href="#"
                            class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-primary-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-primary-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-primary-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Links -->
                <div>
                    <h3 class="font-semibold text-lg mb-6">Tienda</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('catalog') }}"
                                class="text-gray-400 hover:text-white transition-colors">Catálogo</a></li>
                        <li><a href="{{ route('catalog', ['featured' => 1]) }}"
                                class="text-gray-400 hover:text-white transition-colors">Destacados</a></li>
                        <li><a href="{{ route('catalog', ['sort' => 'newest']) }}"
                                class="text-gray-400 hover:text-white transition-colors">Novedades</a></li>
                        <li><a href="{{ route('catalog', ['sort' => 'popular']) }}"
                                class="text-gray-400 hover:text-white transition-colors">Más Vendidos</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-lg mb-6">Ayuda</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Preguntas
                                Frecuentes</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Envíos y
                                Devoluciones</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Guía de Tallas</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contacto</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="font-semibold text-lg mb-6">Contacto</h3>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Av. Principal 123, Lima</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>+51 999 999 999</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>tienda@sneakerhub.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="border-gray-800 my-12">

            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">© {{ date('Y') }} SneakerHub. Todos los derechos reservados.</p>
                <div class="flex items-center gap-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-white transition-colors">Términos y Condiciones</a>
                    <a href="#" class="hover:text-white transition-colors">Política de Privacidad</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>