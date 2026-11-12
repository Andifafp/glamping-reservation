<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Booking extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'booking_code', 'user_id', 'room_type_id',
        'contact_name', 'contact_email', 'contact_phone',
        'check_in', 'check_out', 'nights', 'units', 'guests',
        'price_per_night', 'subtotal', 'with_insurance', 'insurance_fee', 'total_price',
        'status', 'notes', 'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'check_in'       => 'date',
            'check_out'      => 'date',
            'with_insurance' => 'boolean',
            'confirmed_at'   => 'datetime',
        ];
    }

    // ---------- Relasi ----------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(Cancellation::class);
    }

    /** Pengajuan pembatalan terakhir (untuk ditampilkan di profil). */
    public function latestCancellation(): HasOne
    {
        return $this->hasOne(Cancellation::class)->latestOfMany();
    }

    // ---------- Scope ----------

    /** Booking yang sedang menahan unit (belum batal, belum selesai). */
    public function scopeHoldingUnits(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Booking yang tanggalnya beririsan dengan [checkIn, checkOut).
     * Tanggal check-out tidak dihitung menginap, jadi tamu yang check-out
     * tanggal 10 tidak bentrok dengan tamu yang check-in tanggal 10.
     */
    public function scopeOverlapping(Builder $query, string $checkIn, string $checkOut): Builder
    {
        return $query->where('check_in', '<', $checkOut)
                     ->where('check_out', '>', $checkIn);
    }

    // ---------- Helper ----------

    public static function generateCode(): string
    {
        do {
            $code = 'GLM-' . strtoupper(Str::random(6));
        } while (self::where('booking_code', $code)->exists());

        return $code;
    }

    /**
     * Boleh diajukan pembatalan jika: sudah dibayar (confirmed), check-in belum lewat,
     * dan tidak ada pengajuan pembatalan yang masih menunggu.
     * Pastikan relasi latestCancellation sudah di-load agar tidak query berulang.
     */
    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_CONFIRMED
            && $this->check_in->isAfter(today())
            && $this->latestCancellation?->status !== Cancellation::STATUS_PENDING;
    }

    /**
     * Persentase refund jika dibatalkan pada tanggal $requestedAt.
     * Aturan diambil dari config/glamping.php.
     */
    public function refundPercentageAt(Carbon $requestedAt): int
    {
        $daysBefore = $requestedAt->copy()->startOfDay()
            ->diffInDays($this->check_in->copy()->startOfDay(), false);

        $beforeThreshold = $daysBefore > config('glamping.refund_threshold_days');
        $rule = $this->with_insurance ? 'with_insurance' : 'without_insurance';

        return (int) config("glamping.refund.$rule." . ($beforeThreshold ? 'before_threshold' : 'after_threshold'));
    }
}
