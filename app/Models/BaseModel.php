<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function checkModelBelongsToMe(object $model, string $relationship, bool $abort = true): bool
    {
        $myModels = $this->{$relationship};
        if( ! $myModels->contains($model) ){
            if($abort) abort(403, message: 'This action is unauthorized.');
            return false;
        }
        return true;
    }
}
