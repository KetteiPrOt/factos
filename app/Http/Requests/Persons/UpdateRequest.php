<?php

namespace App\Http\Requests\Persons;

use App\Models\Persons\IdentificationType;
use App\Rules\String\ExactSizes;
use App\Rules\Unique\For\Rule as UniqueFor;
use App\Rules\String\NumericDigits;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = User::find(Auth::user()->id);
        return $user->checkModelBelongsToMe(
            $this->route('person'),
            relationship: 'persons',
            abort: false
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $person = $this->route('person');
        $user = Auth::user();
        $final_consumer_id = IdentificationType::finalConsumer()->id;
        return [
            'identification' => [
                'bail', 'required', 'string', 'min:10', 'max:13',
                new NumericDigits, new UniqueFor(
                    $user, relation: 'persons', ignore: $person->id
                ), new ExactSizes(10, 13)
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
}
