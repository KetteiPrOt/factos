<?php

namespace App\Models\Receipts;

use App\Models\Establishments\Sequential;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Type extends Model
{
    public $timestamps = false;

    protected $table = 'receipt_types';

    protected $fillable = ['code', 'name'];

    public function sequentials(): HasMany
    {
        return $this->hasMany(Sequential::class);
    }
}