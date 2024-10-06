<?php

namespace App\Models\Establishments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Establishments\Model as Establishment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\Establishments\IssuancePointFactory as Factory;

class IssuancePoint extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'issuance_points';

    protected $fillable = [
        'code',
        'description',
        'active',
        'establishment_id'
    ];

    protected static function newFactory()
    {
        return Factory::new();
    }

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    public function sequentials(): HasMany
    {
        return $this->hasMany(Sequential::class);
    }
}
