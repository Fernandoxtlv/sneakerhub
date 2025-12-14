@extends('layouts.admin')

@section('title', 'Movimientos de Stock')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Movimientos de Stock</h1>
            <p class="text-gray-500">Historial de entradas y salidas de inventario</p>
        </div>
        <button x-data x-on:click="$dispatch('open-modal-stock-movement')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Movimiento
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('admin.stock-movements.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por producto..." 
                   class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500">
            
            <select name="type" class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50">
                <option value="">Todos los tipos</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Entrada</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Salida</option>
            </select>
            
            <select name="reason" class="px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50">
                <option value="">Todos los motivos</option>
                <option value="sale" {{ request('reason') == 'sale' ? 'selected' : '' }}>Venta</option>
                <option value="purchase" {{ request('reason') == 'purchase' ? 'selected' : '' }}>Compra</option>
                <option value="return" {{ request('reason') == 'return' ? 'selected' : '' }}>Devolución</option>
                <option value="adjustment" {{ request('reason') == 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                <option value="damage" {{ request('reason') == 'damage' ? 'selected' : '' }}>Daño</option>
            </select>
            
            <button type="submit" class="px-4 py-2.5 bg-gray-900 text-white rounded-xl hover:bg-gray-800 transition-colors font-medium">Filtrar</button>
        </form>
    </div>

    {{-- Movements Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Cantidad</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Motivo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Stock Final</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-600">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($movement->product && $movement->product->mainImage)
                                            <img src="{{ asset('storage/' . $movement->product->mainImage->path) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $movement->product->name ?? 'Producto eliminado' }}</p>
                                        <p class="text-sm text-gray-500">{{ $movement->product->sku ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($movement->type == 'in')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Entrada</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">Salida</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold {{ $movement->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movement->type == 'in' ? '+' : '-' }}{{ $movement->quantity }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $reasonLabels = [
                                        'sale' => 'Venta',
                                        'purchase' => 'Compra',
                                        'return' => 'Devolución',
                                        'adjustment' => 'Ajuste',
                                        'damage' => 'Daño',
                                    ];
                                @endphp
                                <span class="text-gray-600">{{ $reasonLabels[$movement->reason] ?? $movement->reason }}</span>
                            </td>
                            <td class="px-6 py-4 font-mono font-medium text-gray-900">{{ $movement->stock_after ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $movement->user->name ?? 'Sistema' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <p class="text-gray-500">No hay movimientos de stock</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($movements->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $movements->links() }}
            </div>
        @endif
    </div>
</div>

{{-- New Movement Modal --}}
<div x-data="{ open: false }" x-on:open-modal-stock-movement.window="open = true">
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
        <div @click.outside="open = false" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <form action="{{ route('admin.stock-movements.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Nuevo Movimiento de Stock</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <x-select name="product_id" label="Producto" :options="$products->pluck('name', 'id')" required />
                    
                    <x-select name="type" label="Tipo" :options="['in' => 'Entrada', 'out' => 'Salida']" required />
                    
                    <x-input type="number" name="quantity" label="Cantidad" min="1" required />
                    
                    <x-select name="reason" label="Motivo" :options="['purchase' => 'Compra/Reposición', 'return' => 'Devolución', 'adjustment' => 'Ajuste Manual', 'damage' => 'Daño/Pérdida']" required />
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="px-5 py-2.5 text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 font-medium">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
