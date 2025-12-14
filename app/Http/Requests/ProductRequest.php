<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'sizes_available' => 'nullable|array',
            'sizes_available.*' => 'integer|min:20|max:60',
            'color' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
            'gender' => 'nullable|in:men,women,unisex,kids',
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'is_new' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,webp|max:10240',
        ];

        // For update, make SKU unique except for current product
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['sku'] = 'nullable|string|max:50|unique:products,sku,' . $this->route('product')->id;
        } else {
            $rules['sku'] = 'nullable|string|max:50|unique:products,sku';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'brand_id.required' => 'Selecciona una marca.',
            'brand_id.exists' => 'La marca seleccionada no existe.',
            'category_id.required' => 'Selecciona una categoría.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número.',
            'price.min' => 'El precio no puede ser negativo.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser negativo.',
            'discount.max' => 'El descuento no puede ser mayor a 100%.',
            'images.*.image' => 'El archivo debe ser una imagen.',
            'images.*.mimes' => 'Las imágenes deben ser JPEG, PNG o WebP.',
            'images.*.max' => 'Las imágenes no pueden pesar más de 10MB.',
        ];
    }
}
