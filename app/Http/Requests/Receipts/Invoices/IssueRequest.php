<?php

namespace App\Http\Requests\Receipts\Invoices;

use App\Models\Establishments\IssuancePoint;
use App\Rules\Model\BelgonsToUser;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Establishments\Model as Establishment;
use App\Models\Products\Model as Product;
use App\Models\Persons\IdentificationType;
use App\Rules\Entities\Receipts\Invoices\Discount;
use App\Rules\Entities\Receipts\Invoices\PaymentValue;
use App\Rules\Model\BelgonsTo;
use App\Rules\String\ExactSizes;
use App\Rules\String\NumericDigits;
use Illuminate\Validation\Rule;

class IssueRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $today = date('Y-m-d');
        $threeMonthAgo = date('Y-m-d', mktime(date('H'), month: date('n') - 3));
        $finalConsumer = IdentificationType::where('name', 'VENTA A CONSUMIDOR FINAL')->first()->id;
        return [
            // Origin Section
            'establishment_id' => [
                'bail', 'required', 'exists:establishments,id',
                new BelgonsToUser(Establishment::class, relationship: 'establishments')
            ],
            'issuance_date' => [
                'required', 'date_format:Y-m-d',
                "after_or_equal:$threeMonthAgo",
                "before_or_equal:$today"
            ],
            'issuance_point_id' => [
                'bail', 'required', 'exists:issuance_points,id',
                new BelgonsTo(
                    Establishment::find($this->get('establishment_id')),
                    IssuancePoint::class,
                    relationship: 'issuancePoints'
                )
            ],
            // Client Section
            'identification_type_id' => 'required|integer|exists:identification_types,id',
            'identification' => [
                'bail', "required_unless:identification_type_id,$finalConsumer",
                'string', 'min:10', 'max:13', new NumericDigits, new ExactSizes(10, 13),
                "exclude_if:identification_type_id,$finalConsumer"
            ],
            'social_reason' => [
                "required_unless:identification_type_id,$finalConsumer", 'string', 'max:255',
                "exclude_if:identification_type_id,$finalConsumer"
            ],
            'phone_number' => [
                'bail', 'string', 'size:10', new NumericDigits,
                "exclude_if:identification_type_id,$finalConsumer"
            ],
            'address' => "string|max:255|exclude_if:identification_type_id,$finalConsumer",
            'email' => [
                "required_unless:identification_type_id,$finalConsumer",
                'string', 'max:255', 'email:rfc,strict',
                "exclude_if:identification_type_id,$finalConsumer"
            ],
            // Details Section
            'products' => 'required|array|max:100',
            'products.*' => 'required|array:product_id,amount,price,discount|size:4',
            'products.*.product_id' => [
                'bail', 'required', 'exists:products,id', 'distinct',
                new BelgonsToUser(Product::class, relationship: 'products')
            ],
            'products.*.amount' => 'required|integer|min:1|max:9999999',
            'products.*.price' => 'required|decimal:0,2|min:0|max:999999.99',
            'products.*.discount' => [
                'bail', 'required', 'decimal:0,2', 'min:0', 'max:999999.99', new Discount
            ],
            // Totals Section
            'tip_ten_percent' => 'sometimes|accepted',
            // Payment Methods Section
            'payment_methods' => 'required|array|max:10',
            'payment_methods.*' => 'required|array:pay_method_id,value,term,time|size:4',
            'payment_methods.*.pay_method_id' => 'required|integer|exists:pay_methods,id|distinct',
            'payment_methods.*.value' => [
                'bail', 'required', 'decimal:0,2', 'min:0', 'max:999999.99', new PaymentValue
            ],
            'payment_methods.*.term' => 'integer|min:1|max:99999',
            'payment_methods.*.time' => [
                'string', Rule::in(['Años', 'Meses', 'Días'])
            ],
            // Additional Fields Section
            'additional_fields' => 'array|max:10',
            'additional_fields.*' => 'array:name,description|size:2',
            'additional_fields.*.name' => 'required|string|max:255',
            'additional_fields.*.description' => 'required|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            // Origin Section
            'establishment_id' => 'establecimiento',
            'issuance_date' => 'fecha de emisión',
            'issuance_point_id' => 'punto de emisión',
            // Client Section
            'identification_type_id' => 'tipo de identificación',
            'identification' => 'identificación',
            'social_reason' => 'razón social',
            'phone_number' => 'número de teléfono',
            'address' => 'dirección',
            'email' => 'email',
            // Details Section
            'products' => 'detalles',
            'products.*' => 'detalle #:position',
            'products.*.product_id' => 'producto #:position',
            'products.*.amount' => 'cantidad #:position',
            'products.*.price' => 'precio #:position',
            'products.*.discount' => 'descuento #:position',
            // Payment Methods Section
            'payment_methods' => 'métodos de pago',
            'payment_methods.*' => 'método de pago #:position',
            'payment_methods.*.pay_method_id' => 'método de pago #:position',
            'payment_methods.*.value' => 'valor del método de pago #:position',
            'payment_methods.*.term' => 'palzo del método de pago #:position',
            'payment_methods.*.time' => 'tiempo del método de pago #:position'
        ];
    }
}
