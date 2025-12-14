@extends('layouts.admin')

@section('title', 'Reportes')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reportes</h1>
                <p class="text-gray-500">Analiza el rendimiento de tu tienda</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.reports.export-csv') }}?{{ http_build_query(request()->all()) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 text-white font-medium rounded-xl hover:bg-green-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exportar CSV
                </a>
                <a href="{{ route('admin.reports.export-pdf') }}?{{ http_build_query(request()->all()) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 text-white font-medium rounded-xl hover:bg-red-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Exportar PDF
                </a>
            </div>
        </div>

        {{-- Date Filter --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
            <form action="{{ route('admin.reports.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input type="date" name="from" value="{{ request('from', now()->subMonth()->format('Y-m-d')) }}"
                        class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
                        class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50">
                </div>
                <button type="submit"
                    class="px-6 py-2.5 bg-indigo-500 text-white font-medium rounded-xl hover:bg-indigo-600 transition-colors">Aplicar</button>
            </form>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white">
                <p class="text-white/80 text-sm font-medium mb-2">Ventas Totales</p>
                <p class="text-3xl font-bold">{{ $stats['total_orders'] ?? 0 }}</p>
                <p class="text-white/80 text-sm mt-2">pedidos en el período</p>
            </div>
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white">
                <p class="text-white/80 text-sm font-medium mb-2">Ingresos</p>
                <p class="text-3xl font-bold">S/ {{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
                <p class="text-white/80 text-sm mt-2">en ventas</p>
            </div>
            <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-white">
                <p class="text-white/80 text-sm font-medium mb-2">Ticket Promedio</p>
                <p class="text-3xl font-bold">S/ {{ number_format($stats['avg_ticket'] ?? 0, 2) }}</p>
                <p class="text-white/80 text-sm mt-2">por pedido</p>
            </div>
            <div class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl p-6 text-white">
                <p class="text-white/80 text-sm font-medium mb-2">Productos Vendidos</p>
                <p class="text-3xl font-bold">{{ $stats['total_products_sold'] ?? 0 }}</p>
                <p class="text-white/80 text-sm mt-2">unidades</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Payment Methods Chart --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ventas por Método de Pago</h2>
                <div class="space-y-4">
                    @php
                        $cashTotal = $stats['cash_revenue'] ?? 0;
                        $yapeTotal = $stats['yape_revenue'] ?? 0;
                        $total = $cashTotal + $yapeTotal;
                        $cashPercent = $total > 0 ? ($cashTotal / $total) * 100 : 0;
                        $yapePercent = $total > 0 ? ($yapeTotal / $total) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Efectivo</span>
                            <span class="font-medium">S/ {{ number_format($cashTotal, 2) }}</span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ $cashPercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-600">Yape</span>
                            <span class="font-medium">S/ {{ number_format($yapeTotal, 2) }}</span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full" style="width: {{ $yapePercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top Products --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Top 10 Productos</h2>
                <div class="space-y-3">
                    @forelse($topProducts ?? [] as $product)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 truncate flex-1">{{ $product->name }}</span>
                            <span class="font-medium text-gray-900 ml-2">{{ $product->total_sold }} uds.</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No hay datos disponibles</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Stock Alerts --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Stock Crítico</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Producto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Stock</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($lowStockProducts ?? [] as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-gray-600 font-mono text-sm">{{ $product->sku }}</td>
                                <td class="px-4 py-3 font-bold {{ $product->stock <= 0 ? 'text-red-600' : 'text-amber-600' }}">
                                    {{ $product->stock }}</td>
                                <td class="px-4 py-3">
                                    @if($product->stock <= 0)
                                        <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">Sin
                                            stock</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-amber-100 text-amber-700 text-sm font-medium rounded-full">Bajo</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay productos con stock bajo</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection