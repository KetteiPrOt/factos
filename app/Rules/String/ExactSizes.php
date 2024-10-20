<?php

namespace App\Rules\String;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class ExactSizes implements ValidationRule
{
    /**
     * The sizes it can have
     */
    private array $sizes;

    public function __construct(...$sizes)
    {
        $this->sizes = $sizes;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $invalid = true;
        foreach($this->sizes as $size){
            if(Str::length($value) == $size){
                $invalid = false; break;
            }
        }
        if($invalid) $fail($this->message());
    }

    private function message(): string
    {
        $sizes = Arr::join($this->sizes, ', ', ' o ');
        return "El campo :attribute debe tener $sizes carácteres de largo.";
    }
}
