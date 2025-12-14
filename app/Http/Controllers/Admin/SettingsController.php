<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'store' => Setting::getByGroup('store'),
            'tax' => Setting::getByGroup('tax'),
            'shipping' => Setting::getByGroup('shipping'),
            'payment' => Setting::getByGroup('payment'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            // Store settings
            'store_name' => 'required|string|max:255',
            'store_ruc' => 'required|string|max:15',
            'store_address' => 'required|string|max:500',
            'store_phone' => 'required|string|max:20',
            'store_email' => 'required|email|max:255',

            // Tax settings
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_name' => 'required|string|max:50',

            // Shipping settings
            'delivery_fee' => 'required|numeric|min:0',
            'free_delivery_threshold' => 'nullable|numeric|min:0',

            // Payment settings
            'yape_enabled' => 'boolean',
            'yape_phone' => 'nullable|string|max:20',

            // Stock settings
            'stock_alert_threshold' => 'required|integer|min:1|max:100',
        ]);

        // Store settings
        Setting::setValue('store_name', $validated['store_name'], 'string', 'store');
        Setting::setValue('store_ruc', $validated['store_ruc'], 'string', 'store');
        Setting::setValue('store_address', $validated['store_address'], 'string', 'store');
        Setting::setValue('store_phone', $validated['store_phone'], 'string', 'store');
        Setting::setValue('store_email', $validated['store_email'], 'string', 'store');

        // Tax settings
        Setting::setValue('tax_rate', $validated['tax_rate'], 'float', 'tax');
        Setting::setValue('tax_name', $validated['tax_name'], 'string', 'tax');

        // Shipping settings
        Setting::setValue('delivery_fee', $validated['delivery_fee'], 'float', 'shipping');
        Setting::setValue('free_delivery_threshold', $validated['free_delivery_threshold'] ?? 0, 'float', 'shipping');

        // Payment settings
        Setting::setValue('yape_enabled', $validated['yape_enabled'] ?? false, 'boolean', 'payment');
        Setting::setValue('yape_phone', $validated['yape_phone'] ?? '', 'string', 'payment');

        // Stock settings
        Setting::setValue('stock_alert_threshold', $validated['stock_alert_threshold'], 'integer', 'general');

        return back()->with('success', 'Configuración actualizada exitosamente.');
    }
}
