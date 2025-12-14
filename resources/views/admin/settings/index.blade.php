@extends('layouts.admin')

@section('title', 'Configuración')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuración</h1>
            <p class="text-gray-500">Configura los parámetros de tu tienda</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Store Settings --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Información de la Tienda</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="settings[store_name]" label="Nombre de la Tienda" :value="$settings['store_name'] ?? 'SneakerHub'" required />
                    <x-input name="settings[store_email]" label="Email de Contacto" :value="$settings['store_email'] ?? ''"
                        type="email" />
                    <x-input name="settings[store_phone]" label="Teléfono" :value="$settings['store_phone'] ?? ''" />
                    <x-input name="settings[store_ruc]" label="RUC" :value="$settings['store_ruc'] ?? ''" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                    <textarea name="settings[store_address]" rows="2"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 bg-gray-50 hover:bg-white focus:bg-white transition-all">{{ $settings['store_address'] ?? '' }}</textarea>
                </div>
            </div>

            {{-- Financial Settings --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Configuración Financiera</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-input name="settings[currency]" label="Moneda" :value="$settings['currency'] ?? 'PEN'" />
                    <x-input type="number" name="settings[tax_rate]" label="Impuesto (%)" :value="$settings['tax_rate'] ?? 18" step="0.01" min="0" max="100" />
                    <x-input type="number" name="settings[delivery_fee]" label="Costo de Envío (S/)"
                        :value="$settings['delivery_fee'] ?? 10" step="0.01" min="0" />
                </div>

                <x-input type="number" name="settings[free_shipping_threshold]" label="Envío gratis desde (S/)"
                    :value="$settings['free_shipping_threshold'] ?? 200" step="0.01" min="0"
                    hint="Monto mínimo para envío gratuito. Dejar 0 para deshabilitar." />
            </div>

            {{-- Invoice Settings --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Boletas / Facturas</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="settings[invoice_prefix]" label="Prefijo de Boleta" :value="$settings['invoice_prefix'] ?? 'B'" />
                    <x-input type="number" name="settings[invoice_next_number]" label="Próximo Número"
                        :value="$settings['invoice_next_number'] ?? 1" min="1" />
                </div>
            </div>

            {{-- Yape Settings --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Configuración de Yape</h2>

                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl mb-4">
                    <p class="text-sm text-amber-700">
                        <strong>Nota:</strong> Esta es una configuración de simulación. Para integrar Yape en producción,
                        contacta a Yape para obtener credenciales reales y configura el webhook endpoint.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-input name="settings[yape_phone]" label="Número de Yape" :value="$settings['yape_phone'] ?? ''"
                        placeholder="999888777" />
                    <x-input name="settings[yape_name]" label="Nombre en Yape" :value="$settings['yape_name'] ?? ''"
                        placeholder="SneakerHub Store" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Webhook URL</label>
                    <div class="flex items-stretch gap-2">
                        <input type="text" value="{{ url('/api/webhooks/yape') }}" readonly
                            class="flex-1 px-4 py-3 border border-gray-200 rounded-xl bg-gray-100 text-gray-600">
                        <button type="button" onclick="navigator.clipboard.writeText('{{ url('/api/webhooks/yape') }}')"
                            class="px-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-colors">
                            Copiar
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Usa este URL para configurar el webhook de Yape</p>
                </div>
            </div>

            {{-- Stock Settings --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-6">
                <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-4">Inventario</h2>

                <x-input type="number" name="settings[low_stock_threshold]" label="Umbral de Stock Bajo"
                    :value="$settings['low_stock_threshold'] ?? 5" min="1"
                    hint="Se mostrará alerta cuando el stock sea igual o menor a este valor" />
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-medium rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg shadow-indigo-500/30">
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>
@endsection