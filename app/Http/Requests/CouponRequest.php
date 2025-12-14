<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon')?->id;

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:coupons,code,' . $couponId,
            ],
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'El código del cupón es obligatorio.',
            'code.unique' => 'Este código de cupón ya existe.',
            'type.required' => 'El tipo de descuento es obligatorio.',
            'type.in' => 'El tipo debe ser porcentaje o monto fijo.',
            'value.required' => 'El valor del descuento es obligatorio.',
            'value.min' => 'El valor debe ser mayor o igual a 0.',
            'expires_at.after' => 'La fecha de expiración debe ser posterior a la fecha de inicio.',
        ];
    }
}
