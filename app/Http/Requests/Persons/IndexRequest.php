<?php

namespace App\Http\Requests\Persons;

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
            'identification' => 'nullable|string|max:15',
            'social_reason' => 'nullable|string|max:255'
        ];
    }

    public function attributes()
    {
        return [
            'identification' => 'identificación',
            'social_reason' => 'razón social',
        ];
    }
}
