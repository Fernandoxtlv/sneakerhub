{{-- Header Component --}}
<header class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 shadow-2xl sticky top-0 z-50">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Logo --}}
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span
                        class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">SneakerHub</span>
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center space-x-1">
                <a href="{{ route('catalog') }}"
                    class="px-4 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 font-medium">
                    Catálogo
                </a>
                @php
                    $categories = \App\Models\Category::take(4)->get();
                @endphp
                @foreach($categories as $category)
                    <a href="{{ route('category.show', $category) }}"
                        class="px-4 py-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>

            {{-- Search Bar --}}
            <div class="hidden lg:flex items-center flex-1 max-w-md mx-8">
                <form action="{{ route('search') }}" method="GET" class="w-full">
                    <div class="relative">
                        <input type="text" name="q" placeholder="Buscar zapatillas..."
                            class="w-full bg-white/10 border border-white/20 rounded-full py-2 pl-10 pr-4 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-transparent transition-all duration-300">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>

            {{-- Right Section --}}
            <div class="flex items-center space-x-4">
                {{-- Cart Widget --}}
                <x-cart-widget />

                {{-- User Menu --}}
                @auth
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 text-gray-300 hover:text-white transition-colors">
                            <div
                                class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span
                                    class="text-white font-semibold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-xl shadow-xl border border-white/10 py-2 z-50">
                            <div class="px-4 py-2 border-b border-white/10">
                                <p class="text-white font-medium truncate">{{ auth()->user()->name }}</p>
                                <p class="text-gray-400 text-sm truncate">{{ auth()->user()->email }}</p>
                            </div>
                            @if(auth()->user()->hasAnyRole(['owner', 'admin', 'worker']))
                                <a href="{{ route('admin.dashboard') }}"
                                    class="block px-4 py-2 text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                                    <span class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                        <span>Panel Admin</span>
                                    </span>
                                </a>
                            @endif
                            <a href="{{ route('orders.index') }}"
                                class="block px-4 py-2 text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                                Mis Pedidos
                            </a>
                            <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-gray-300 hover:bg-white/10 hover:text-white transition-colors">
                                Mi Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-red-400 hover:bg-white/10 hover:text-red-300 transition-colors">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition-colors font-medium">
                        Ingresar
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg hover:from-indigo-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 font-medium">
                        Registrarse
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Button --}}
            <div class="md:hidden flex items-center">
                <button x-data x-on:click="$dispatch('toggle-mobile-menu')" class="text-gray-300 hover:text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>
</header>