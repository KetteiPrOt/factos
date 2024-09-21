<?php

namespace App\Http\Requests\Products;

use App\Models\User;
use App\Rules\Products\UniqueFor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product_id = $this->route('product')->id;
        return [
            'code' => ['required', 'string', 'max:25', new UniqueFor(User::find(Auth::user()?->id ?? 1), ignore: $product_id)],
            'name' => 'required|string|max:255',
            'price' => 'required|decimal:0,2|min:0.01|max:999999.99',
            'additional_info' => 'nullable|string|max:255',
            'tourism_vat_applies' => 'sometimes|accepted',
            'ice_applies' => 'sometimes|accepted',
            'ice_type_id' => 'required_with:ice_applies|exclude_without:ice_applies|exists:ice_types,id',
            'vat_rate_id' => 'required|integer|exists:vat_rates,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'codigo',
            'name' => 'nombre',
            'price' => 'precio',
            'additional_info' => 'información adicional',
            'tourism_vat_applies' => 'aplica IVA turismo',
            'ice_applies' => 'ICE',
            'ice_type_id' => 'codigo de ICE',
            'vat_rate_id' => 'tarifa de iva',
        ];
    }
}
