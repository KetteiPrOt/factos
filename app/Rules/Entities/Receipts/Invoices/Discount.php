<?php

namespace App\Rules\Entities\Receipts\Invoices;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;
use App\Models\Products\Model as Product;

class Discount implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {   
        $key = $this->extractKey($attribute);
        $total = $this->calcTotal($key);
        if($value > $total){
            $fail('El descuento no puede superar al valor total.');
        }
    }

    private function extractKey(string $attribute): int
    {
        $key = substr($attribute, mb_strlen('products.'));
        $key = substr($key, 0, length: mb_strpos($key, '.discount'));
        return intval($key);
    }

    private function calcTotal(int $key): float
    {
        $product_data = $this->data['products'][$key];
        return $product_data['amount'] * $product_data['price'];
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
 
        return $this;
    }
}
