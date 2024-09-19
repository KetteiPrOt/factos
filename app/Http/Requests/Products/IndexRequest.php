<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255'
        ];
    }

    public function attributes()
    {
        return [
            'code' => 'código',
            'name' => 'nombre',
        ];
    }
}
