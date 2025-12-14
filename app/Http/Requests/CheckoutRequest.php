<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cash,yape',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_name.required' => 'El nombre del destinatario es obligatorio.',
            'shipping_address.required' => 'La dirección de envío es obligatoria.',
            'shipping_city.required' => 'La ciudad es obligatoria.',
            'shipping_phone.required' => 'El teléfono de contacto es obligatorio.',
            'payment_method.required' => 'Seleccione un método de pago.',
            'payment_method.in' => 'El método de pago seleccionado no es válido.',
        ];
    }
}
