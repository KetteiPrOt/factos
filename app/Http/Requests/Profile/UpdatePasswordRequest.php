<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|string|max:255|current_password',
            'new_password' => [
                'required', 'string',
                Password::min(8)->max(255)
                    ->uncompromised()->mixedCase()->letters()->numbers()->symbols(),
                'confirmed'
            ]
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'contraseña actual',
            'new_password' => 'nueva contraseña'
        ];
    }
}
