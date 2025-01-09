<?php

namespace App\Http\Requests\Invoices\Receipts;

use App\Rules\String\NumericDigits;
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
            'date_from' => 'sometimes|date_format:Y-m-d',
            'date_to' => 'sometimes|date_format:Y-m-d',
            'access_key' => ['bail', 'sometimes', 'string', 'max:49', new NumericDigits()]
        ];
    }

    public function attributes(): array
    {
        return [
            'date_from' => 'fecha inicial',
            'date_to' => 'fecha final',
            'access_key' => 'clave de acceso'
        ];
    }
}
