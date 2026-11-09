<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID    = 'paid';
    public const STATUS_FAILED  = 'failed';

    protected $fillable = ['booking_id', 'reference', 'method', 'amount', 'status', 'paid_at'];

    protected function casts(): array
    {
        return [
            'amount'  => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
