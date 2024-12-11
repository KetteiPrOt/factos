<?php

namespace App\Rules\Model;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class BelgonsToUser implements ValidationRule
{
    private string $Model;

    private string $relationship;

    public function __construct(string $Model, string $relationship)
    {
        $this->Model = $Model;
        $this->relationship = $relationship;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $model = $this->Model::find($value);
        $user = User::find(Auth::user()->id);
        if(
            ! $user->checkModelBelongsToMe($model, $this->relationship, abort: false)
        ){
            $fail('El :attribute seleccionado no esta autorizado.');
        };
    }
}
