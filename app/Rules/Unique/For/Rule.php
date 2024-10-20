<?php

namespace App\Rules\Unique\For;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Rule implements ValidationRule
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
            function(object $model, int $key) use ($value, $ignore_id, $attribute) {
                return is_null($ignore_id)
                    ? $model->{$attribute} == $value
                    : $model->{$attribute} == $value && $model->id != $ignore_id;
            }
        );
        if($not_unique){
            $fail('El campo :attribute ya ha sido registrado.');
        }
    }
}
