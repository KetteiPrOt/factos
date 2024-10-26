<?php

namespace App\Http\Requests\Persons;

use App\Models\Persons\IdentificationType;
use App\Rules\String\ExactSizes;
use App\Rules\Unique\For\Rule as UniqueFor;
use App\Rules\String\NumericDigits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
        $final_consumer_id = IdentificationType::finalConsumer()->id;
        return [
            'identification' => [
                'bail', 'required', 'string', 'min:10', 'max:13',
                new NumericDigits, new UniqueFor($user, relation: 'persons'),
                new ExactSizes(10, 13)
            ],
            'social_reason' => 'required|string|max:255',
            'email' => 'required|string|max:255|email:rfc,strict',
            'phone_number' => ['bail', 'nullable', 'string', 'size:10', new NumericDigits],
            'address' => 'nullable|string|max:255',
            'identification_type_id' => [
                'required', 'integer', 'exists:identification_types,id',
                Rule::notIn($final_consumer_id)
            ]
        ];
    }

    public function attributes(): array
    {
        return [
            'identification' => 'identificación',
            'social_reason' => 'razón social',
            'email' => 'correo electrónico',
            'phone_number' => 'número de teléfono',
            'address' => 'dirección',
            'identification_type_id' => 'tipo de identificación'
        ];
    }
}
