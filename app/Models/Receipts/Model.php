<?php

namespace App\Models\Receipts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Model extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'access_key',
        'issuance_date',
        'number',
        'status',
        'content',
        'user_id',
        'receipt_type_id'
    ];

    public function receiptType(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
