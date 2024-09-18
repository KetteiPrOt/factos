<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class VatRate extends Model
{
    protected $table = 'vat_rates';

    protected $fillable = ['code', 'percentaje', 'name'];

    public $timestamps = false;
}
