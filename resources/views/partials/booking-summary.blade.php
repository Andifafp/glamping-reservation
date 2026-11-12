{{-- Rincian booking (dipakai di halaman bayar, sukses, dan pembatalan). Parameter: $booking --}}
<dl class="space-y-3 text-sm">
    <div class="flex justify-between gap-4">
        <dt class="text-palung/65">Kamar</dt>
        <dd class="text-right font-medium">{{ $booking->roomType->name }}</dd>
    </div>
    <div class="flex justify-between gap-4">
        <dt class="text-palung/65">Tanggal</dt>
        <dd class="text-right font-medium">
            {{ $booking->check_in->translatedFormat('d M Y') }} sampai {{ $booking->check_out->translatedFormat('d M Y') }}
        </dd>
    </div>
    <div class="flex justify-between gap-4">
        <dt class="text-palung/65">Lama menginap</dt>
        <dd class="font-medium">{{ $booking->nights }} malam, {{ $booking->units }} unit, {{ $booking->guests }} tamu</dd>
    </div>
    <div class="flex justify-between gap-4">
        <dt class="text-palung/65">Harga kamar</dt>
        <dd class="font-medium">Rp{{ number_format($booking->subtotal, 0, ',', '.') }}</dd>
    </div>
    <div class="flex justify-between gap-4">
        <dt class="text-palung/65">Asuransi</dt>
        <dd class="font-medium">
            @if ($booking->with_insurance)
                Rp{{ number_format($booking->insurance_fee, 0, ',', '.') }}
            @else
                Tidak ikut
            @endif
        </dd>
    </div>
    <div class="flex justify-between gap-4 border-t border-palung/10 pt-4 text-base">
        <dt class="font-semibold">Total</dt>
        <dd class="font-display text-2xl">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</dd>
    </div>
</dl>
