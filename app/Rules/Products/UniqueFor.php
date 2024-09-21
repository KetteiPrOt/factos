<?php

namespace App\Rules\Products;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Products\Model as Product;

class UniqueFor implements ValidationRule
{
    private User $user;

    private ?int $ignore;

    public function __construct(User $user, ?int $ignore = null)
    {
        $this->user = $user;
        $this->ignore = $ignore;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $ignore_id = $this->ignore;
        $not_unique = $this->user->products->contains(
            function(Product $product, int $key) use ($value, $ignore_id) {
                return is_null($ignore_id)
                    ? $product->code == $value
                    : $product->code == $value && $product->id != $ignore_id;
            }
        );
        if($not_unique){
            $fail('El campo código ya ha sido registrado.');
        }
    }
}
