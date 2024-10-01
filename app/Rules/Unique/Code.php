<?php

namespace App\Rules\Unique;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Code implements ValidationRule
{
    protected User $user;

    /**
     * The name of the relationship between the user and the model
     */
    protected string $relation;

    protected ?int $ignore;

    public function __construct(User $user, string $relation, ?int $ignore = null)
    {
        $this->user = $user;
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
        $not_unique = $this->user->{$this->relation}->contains(
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
