<?php

namespace App\Models\Persons;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\PersonFactory;

class Model extends BaseModel
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'identification',
        'social_reason',
        'email',
        'phone_number',
        'address',
        'identification_type_id',
        'user_id',
    ];

    protected static function newFactory(): Factory
    {
        return PersonFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function identificationType(): BelongsTo
    {
        return $this->belongsTo(IdentificationType::class);
    }
}
