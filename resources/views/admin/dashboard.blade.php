<x-layouts.admin :title="'Dashboard'" :header="'Dashboard'">
    <div class="space-y-8">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-label">Ventas Hoy</p>
                        <p class="stat-value">{{ $kpis['sales_today'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-label">Ingresos Hoy</p>
                        <p class="stat-value">{{ config('sneakerhub.currency.symbol') }}
                            {{ number_format($kpis['revenue_today'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-label">Ingresos del Mes</p>
                        <p class="stat-value">{{ config('sneakerhub.currency.symbol') }}
                            {{ number_format($kpis['revenue_month'], 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-label">Ticket Promedio</p>
                        <p class="stat-value">{{ config('sneakerhub.currency.symbol') }}
                            {{ number_format($kpis['average_ticket'], 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Pending Orders -->
            <div class="bg-orange-50 border border-orange-200 rounded-2xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-orange-700">{{ $kpis['pending_orders'] }}</p>
                        <p class="text-sm text-orange-600">Pedidos pendientes</p>
                    </div>
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                        class="ml-auto btn btn-sm bg-orange-500 text-white hover:bg-orange-600">
                        Ver pedidos
                    </a>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-red-700">
                            {{ $kpis['low_stock_products'] + $kpis['out_of_stock_products'] }}</p>
                        <p class="text-sm text-red-600">Productos con stock bajo</p>
                    </div>
                    <a href="{{ route('admin.products.index', ['status' => 'low_stock']) }}"
                        class="ml-auto btn btn-sm bg-red-500 text-white hover:bg-red-600">
                        Revisar stock
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Orders -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Pedidos Recientes</h2>
                    <a href="{{ route('admin.orders.index') }}"
                        class="text-sm text-primary-600 hover:text-primary-700 font-medium">Ver todos →</a>
                </div>

                <div class="space-y-4">
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-{{ $order->status_color }}-100 rounded-full flex items-center justify-center">
                                    <span class="text-{{ $order->status_color }}-600 font-semibold text-sm">#</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $order->order_number }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->customer_name }} •
                                        {{ $order->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">{{ $order->formatted_total }}</p>
                                <span
                                    class="badge badge-{{ $order->status_color }} text-xs">{{ $order->status_label }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">No hay pedidos recientes</p>
                    @endforelse
                </div>
            </div>

            <!-- Low Stock Products -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Productos con Stock Bajo</h2>
                    <a href="{{ route('admin.products.index', ['status' => 'low_stock']) }}"
                        class="text-sm text-primary-600 hover:text-primary-700 font-medium">Ver todos →</a>
                </div>

                <div class="space-y-4">
                    @forelse($lowStockProducts as $product)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden">
                                    @if($product->mainImage)
                                        <img src="{{ Storage::url($product->mainImage->path_thumb) }}"
                                            alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $product->brand->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold {{ $product->stock <= 0 ? 'text-red-600' : 'text-yellow-600' }}">
                                    {{ $product->stock }} unidades
                                </p>
                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="text-xs text-primary-600 hover:text-primary-700">Editar →</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">No hay productos con stock bajo</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>