<?php

namespace App\Http\Requests\Establishments\IssuancePoints;

use App\Models\Receipts\Type as ReceiptType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Unique\Code as UniqueFor;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = User::find(Auth::user()->id);
        return $user->checkModelBelongsToMe(
            model: $this->route('issuancePoint')->establishment,
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
        $issuancePoint = $this->route('issuancePoint');
        $receiptTypes = ReceiptType::all();
        return [
            'code' => [
                'required', 'integer', 'min:1', 'max:999',
                new UniqueFor(
                    $issuancePoint->establishment,
                    relation: 'issuancePoints',
                    ignore: $issuancePoint->id
                )
            ],
            'description' => 'string|max:255',
            'active' => 'required|boolean',
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
            'code' => 'código',
            'description' => 'descripción',
            'active' => 'activo',
            'sequentials' => 'secuenciales',
            'sequentials.*' => 'secuencial #:position'
        ];
    }
}
