<?php

namespace App\Models\Products;

use App\Models\BaseModel as Model;

class IceType extends Model
{
    protected $table = 'ice_types';

    protected $fillable = ['code', 'name'];

    public $timestamps = false;
}
