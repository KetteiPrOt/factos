<?php

namespace App\Http\Requests\Profile;

use App\Rules\String\NumericDigits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'max:255', 'email:rfc,strict',
                Rule::unique('users', 'email')->ignore(Auth::user()->id)
            ],
            'logo' => 'sometimes|file|max:512|image|dimensions: ratio=1/1',
            'ruc' => [
                'bail', 'required', 'string', 'size:13', new NumericDigits,
                Rule::unique('users', 'ruc')->ignore(Auth::user()->id)
            ],
            'matrix_address' => 'required|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'logo.dimensions' => 'El logo debe tener dimensiones de imagen de aspecto cuadrado (1/1).'
        ];
    }
}
