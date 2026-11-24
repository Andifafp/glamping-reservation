{{--
    Detail + proses pengajuan pembatalan (staff) - Thoni
    Variabel: $cancellation (dengan booking.roomType, booking.payments, user, processor)
--}}
@extends('layouts.dashboard')

@php $booking = $cancellation->booking; @endphp

@section('title', 'Pembatalan ' . $booking->booking_code)

@section('actions')
    <a href="{{ route('dashboard.cancellations.index', ['status' => $cancellation->status]) }}" class="btn btn-secondary">Kembali</a>
@endsection

@section('content')
    <div class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
        <div class="space-y-6">
            <section class="panel" aria-labelledby="judul-pengajuan">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 id="judul-pengajuan" class="text-2xl">Pengajuan</h2>
                    @include('partials.status-badge', ['status' => $cancellation->status, 'type' => 'cancellation'])
                </div>
                <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-palung/60">Diajukan oleh</dt>
                        <dd class="font-semibold">{{ $cancellation->user?->name ?? $booking->contact_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-palung/60">Waktu pengajuan</dt>
                        <dd class="font-semibold">{{ $cancellation->created_at->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-palung/60">Alasan</dt>
                        <dd class="mt-1 rounded-xl bg-garam p-4 leading-6">{{ $cancellation->reason }}</dd>
                    </div>
                </dl>

                <div class="mt-5 rounded-xl {{ $cancellation->refund_percentage > 0 ? 'bg-buih text-laut-tua' : 'bg-red-50 text-red-800' }} p-5">
                    <p class="text-sm">Refund sesuai aturan saat diajukan</p>
                    <p class="mt-1 font-display text-3xl">
                        {{ $cancellation->refund_percentage }}%
                        <span class="text-xl">(Rp{{ number_format($cancellation->refund_amount, 0, ',', '.') }})</span>
                    </p>
                    <p class="mt-2 text-sm">
                        {{ $booking->with_insurance ? 'Dengan asuransi' : 'Tanpa asuransi' }},
                        diajukan {{ (int) $cancellation->created_at->copy()->startOfDay()->diffInDays($booking->check_in, false) }} hari sebelum check-in.
                    </p>
                </div>
            </section>

            @if ($cancellation->status === 'pending')
                <section class="panel" aria-labelledby="judul-proses">
                    <h2 id="judul-proses" class="text-2xl">Proses pengajuan</h2>

                    <form method="POST" action="{{ route('dashboard.cancellations.approve', $cancellation) }}" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="refund_note" class="form-label">Catatan refund/deposit <span class="font-normal text-palung/50">(opsional)</span></label>
                            <textarea id="refund_note" name="refund_note" rows="2" class="form-input"
                                      placeholder="Refund {{ $cancellation->refund_percentage }}% ditransfer manual ke rekening tamu">{{ old('refund_note') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-full sm:w-auto"
                                onclick="return confirm('Setujui pembatalan? Booking akan dibatalkan dan unit kamar dilepas.')">
                            Setujui pembatalan
                        </button>
                    </form>

                    <form method="POST" action="{{ route('dashboard.cancellations.reject', $cancellation) }}" class="mt-8 space-y-4 border-t border-palung/10 pt-6" novalidate>
                        @csrf
                        <div>
                            <label for="staff_note" class="form-label">Alasan penolakan</label>
                            <textarea id="staff_note" name="staff_note" rows="2" class="form-input" placeholder="Tampil di profil tamu">{{ old('staff_note') }}</textarea>
                            @error('staff_note') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="btn btn-danger w-full sm:w-auto">Tolak pembatalan</button>
                    </form>
                </section>
            @else
                <section class="panel" aria-labelledby="judul-hasil">
                    <h2 id="judul-hasil" class="text-2xl">Hasil</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div>
                            <dt class="text-palung/60">Diproses oleh</dt>
                            <dd class="font-semibold">{{ $cancellation->processor?->name ?? '-' }}, {{ $cancellation->processed_at?->translatedFormat('d F Y, H:i') }}</dd>
                        </div>
                        @if ($cancellation->refund_note)
                            <div>
                                <dt class="text-palung/60">Catatan refund</dt>
                                <dd>{{ $cancellation->refund_note }}</dd>
                            </div>
                        @endif
                        @if ($cancellation->staff_note)
                            <div>
                                <dt class="text-palung/60">Catatan staf</dt>
                                <dd>{{ $cancellation->staff_note }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>
            @endif
        </div>

        <aside class="panel lg:self-start" aria-labelledby="judul-booking">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 id="judul-booking" class="text-2xl">Booking</h2>
                @include('partials.status-badge', ['status' => $booking->status])
            </div>
            <p class="mt-1 font-mono text-sm text-palung/60">{{ $booking->booking_code }}</p>
            <div class="mt-5 text-sm">
                <p class="font-semibold">{{ $booking->contact_name }}</p>
                <p class="text-palung/60">{{ $booking->contact_email }}, {{ $booking->contact_phone }}</p>
            </div>
            <div class="mt-5">
                @include('partials.booking-summary', ['booking' => $booking])
            </div>
            @if ($booking->payments->isNotEmpty())
                <p class="mt-4 text-xs text-palung/55">
                    Dibayar {{ $booking->payments->first()->paid_at?->translatedFormat('d M Y, H:i') }}
                    via {{ config('glamping.payment_methods.' . $booking->payments->first()->method) }}
                    ({{ $booking->payments->first()->reference }})
                </p>
            @endif
        </aside>
    </div>
@endsection
