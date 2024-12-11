<?php

namespace App\Rules\Entities\Receipts\Invoices;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PaymentValue implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Por hacer:
        // Si este es el último pago, entonces calcula el valor total a pagar
        // de la factura y verifica que sea igual al valor que esta siendo pagado
    }
}
