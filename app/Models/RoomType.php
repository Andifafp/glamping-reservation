<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'facilities',
        'capacity', 'total_units', 'price_per_night', 'image', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'facilities'      => 'array',
            'is_active'       => 'boolean',
            'capacity'        => 'integer',
            'total_units'     => 'integer',
            'price_per_night' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Jumlah unit yang masih tersedia untuk rentang tanggal tertentu.
     * Booking dianggap memakai unit jika statusnya pending atau confirmed
     * dan tanggalnya beririsan dengan rentang yang diminta.
     *
     * @param  string  $checkIn   format Y-m-d
     * @param  string  $checkOut  format Y-m-d
     */
    public function availableUnits(string $checkIn, string $checkOut, ?int $exceptBookingId = null): int
    {
        $used = $this->bookings()
            ->holdingUnits()
            ->overlapping($checkIn, $checkOut)
            ->when($exceptBookingId, fn ($q) => $q->where('id', '!=', $exceptBookingId))
            ->sum('units');

        return max(0, $this->total_units - (int) $used);
    }
}
