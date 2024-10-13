<?php

namespace App\Models\Persons;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Persons\Model as Person;

class IdentificationType extends BaseModel
{
    public $timestamps = false;

    public static function finalConsumer(): self
    {
        return static::where('code', '07')->first();
    }

    public function persons(): HasMany
    {
        return $this->hasMany(Person::class);
    }
}
