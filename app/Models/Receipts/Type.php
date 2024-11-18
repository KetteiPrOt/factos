<?php

namespace App\Models\Receipts;

use App\Models\Establishments\Sequential;
use App\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Receipts\Model as Receipt;

class Type extends Model
{
    public $timestamps = false;

    protected $table = 'receipt_types';

    protected $fillable = ['code', 'name'];

    public function sequentials(): HasMany
    {
        return $this->hasMany(Sequential::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
}
