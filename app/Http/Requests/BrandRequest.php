<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandId = $this->route('brand')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:brands,name,' . $brandId,
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:brands,slug,' . $brandId,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la marca es obligatorio.',
            'name.unique' => 'Ya existe una marca con este nombre.',
            'slug.unique' => 'Ya existe una marca con este slug.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->slug) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }
}
