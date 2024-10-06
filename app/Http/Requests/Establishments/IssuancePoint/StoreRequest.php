<?php

namespace App\Http\Requests\Establishments\IssuancePoint;

use App\Models\Receipts\Type as ReceiptType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Unique\Code as UniqueFor;
use Illuminate\Support\Facades\Auth;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = User::find(Auth::user()->id);
        return $user->checkModelBelongsToMe(
            model: $this->route('establishment'),
            relationship: 'establishments',
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
        $receiptTypes = ReceiptType::all();
        return [
            'code' => [
                'required', 'integer', 'min:1', 'max:999',
                new UniqueFor($this->route('establishment'), relation: 'issuancePoints')
            ],
            'description' => 'string|max:255',
            'sequentials' => [
                'required', 'array:'.$receiptTypes->implode('id', ','),
                'size:'.$receiptTypes->count()
            ],
            'sequentials.*' => 'required|integer|min:1|max:999999999'
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'Código',
            'description' => 'descripción',
            'sequentials' => 'secuenciales',
            'sequentials.*' => 'secuencial #:position'
        ];
    }
}
