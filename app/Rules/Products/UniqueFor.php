<?php

namespace App\Rules\Products;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueFor implements ValidationRule
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $codes = $this->user->products->pluck('code');
        if($codes->contains($value)){
            $fail('El campo código ya ha sido registrado.');
        }
    }
}
