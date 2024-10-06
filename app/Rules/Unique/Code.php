<?php

namespace App\Rules\Unique;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Code implements ValidationRule
{
    /**
     * The parent model
     */
    protected object $model;

    /**
     * The name of the relationship between the parent model and the child models
     */
    protected string $relation;

    /**
     * Child model's id that will be ignored
     */
    protected ?int $ignore;

    public function __construct(object $model, string $relation, ?int $ignore = null)
    {
        $this->model = $model;
        $this->ignore = $ignore;
        $this->relation = $relation;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $ignore_id = $this->ignore;
        $not_unique = $this->model->{$this->relation}->contains(
            function(object $model, int $key) use ($value, $ignore_id) {
                return is_null($ignore_id)
                    ? $model->code == $value
                    : $model->code == $value && $model->id != $ignore_id;
            }
        );
        if($not_unique){
            $fail('El campo código ya ha sido registrado.');
        }
    }
}
