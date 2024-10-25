<?php

namespace App\Http\Requests\Profile\Certificate;

use Illuminate\Foundation\Http\FormRequest;

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
            'signature' => 'required|file|max:50|extensions:p12',
            'password' => 'required|string|max:255'
        ];
    }
}
