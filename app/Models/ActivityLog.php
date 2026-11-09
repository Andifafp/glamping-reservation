<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    public const UPDATED_AT = null; // log hanya punya created_at

    protected $fillable = [
        'user_id', 'action', 'description',
        'subject_type', 'subject_id', 'properties', 'ip_address', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Pencatat log yang bisa dipanggil dari controller mana pun, contoh:
     * ActivityLog::record('cancellation.approved', "Menyetujui pembatalan {$booking->booking_code}", $cancellation);
     */
    public static function record(string $action, string $description, ?Model $subject = null, array $properties = []): self
    {
        return self::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'description'  => $description,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id'   => $subject?->getKey(),
            'properties'   => $properties ?: null,
            'ip_address'   => request()?->ip(),
        ]);
    }
}
