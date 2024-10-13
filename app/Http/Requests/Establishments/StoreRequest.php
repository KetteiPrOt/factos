<?php

namespace App\Http\Requests\Establishments;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Unique\For\Rule as UniqueFor;
use Illuminate\Support\Facades\Auth;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = Auth::user();
        return [
            'code' => [
                'required', 'integer', 'min:1', 'max:999',
                new UniqueFor($user, relation: 'establishments')
            ],
            'address' => 'required|string|max:255',
            'commercial_name' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'código',
            'address' => 'dirección',
            'commercial_name' => 'nombre comercial'
        ];
    }
}
