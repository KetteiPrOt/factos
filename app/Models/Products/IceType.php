<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class IceType extends Model
{
    protected $table = 'ice_types';

    protected $fillable = ['code', 'name'];

    public $timestamps = false;
}
