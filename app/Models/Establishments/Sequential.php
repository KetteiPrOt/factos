<?php

namespace App\Models\Establishments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Receipts\Type as ReceiptType;
use Database\Factories\Establishments\SequentialFactory as Factory;

class Sequential extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'sequentials';

    protected $fillable = [
        'next',
        'issuance_point_id',
        'receipt_type_id'
    ];

    protected static function newFactory()
    {
        return Factory::new();
    }

    public function issuancePoint(): BelongsTo
    {
        return $this->belongsTo(IssuancePoint::class);
    }

    public function receiptType(): BelongsTo
    {
        return $this->belongsTo(ReceiptType::class);
    }
}
