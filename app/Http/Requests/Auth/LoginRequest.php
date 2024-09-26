<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|max:255',
            'remember_me' => 'sometimes|accepted'
        ];
    }

    public function attributes(): array
    {
        return [
            'password' => 'contraseña',
            'remember_me' => 'recuerdame'
        ];
    }
}
