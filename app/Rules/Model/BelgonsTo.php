<?php

namespace App\Rules\Model;

use App\Models\BaseModel;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BelgonsTo implements ValidationRule
{
    private ?BaseModel $parent;

    private string $Model;

    private string $relationship;

    public function __construct(?BaseModel $parent, string $Model, string $relationship)
    {
        $this->parent = $parent;
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
        if(
            ! $this->parent->checkModelBelongsToMe($model, $this->relationship, abort: false)
        ){
            $fail('El :attribute seleccionado no esta autorizado.');
        };
    }
}
