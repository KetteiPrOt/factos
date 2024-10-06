<?php

namespace App\Models\Products;

use App\Models\User;
use Database\Factories\Products\Factory as ProductFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Model extends BaseModel
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'code',
        'name',
        'price',
        'additional_info',
        'tourism_vat_applies',
        'ice_applies',
        'user_id',
        'ice_type_id',
        'vat_rate_id',
    ];

    protected static function newFactory(): Factory
    {
        return ProductFactory::new();
    }

    public function iceType(): BelongsTo
    {
        return $this->belongsTo(IceType::class);
    }

    public function vatRate(): BelongsTo
    {
        return $this->belongsTo(VatRate::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
