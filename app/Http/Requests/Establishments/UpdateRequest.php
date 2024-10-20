<?php

namespace App\Http\Requests\Establishments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Rules\Unique\For\Rule as UniqueFor;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $establishment = $this->route('establishment');
        return Auth::user()->establishments->contains($establishment);
    }

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
                new UniqueFor(
                    $user,
                    relation: 'establishments',
                    ignore: $this->route('establishment')->id
                )
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
