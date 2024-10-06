<?php

namespace App\Models\Establishments;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\Establishments\Factory;

class Model extends BaseModel
{
    use HasFactory;

    protected $table = 'establishments';

    protected $fillable = [
        'code',
        'address',
        'commercial_name',
        'user_id'
    ];

    protected static function newFactory()
    {
        return Factory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function issuancePoints(): HasMany
    {
        return $this->hasMany(IssuancePoint::class, 'establishment_id');
    }
}
