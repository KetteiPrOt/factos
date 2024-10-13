<?php

namespace App\Rules\String;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class NumericDigits implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        for($i = 0; $i < Str::length($value); $i++){
            $character = $value[$i];
            if( ! is_numeric($character) ) $fail("El campo :attribute no es numérico.");
        }
    }
}
