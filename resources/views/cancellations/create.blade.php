{{--
    Form pengajuan pembatalan - Thoni
    Variabel:
        $booking          : Booking milik user (pastikan $booking->user_id === auth()->id())
        $refundPercentage : $booking->refundPercentageAt(now())
        $refundAmount     : round($booking->total_price * $refundPercentage / 100)
    Form dikirim ke route('cancellations.store', $booking) dengan field: reason
    Simpan dengan status 'pending'. Booking baru menjadi 'cancelled' setelah staff menyetujui.
--}}
@extends('layouts.app')

@section('title', 'Ajukan pembatalan')

@section('content')
    <section class="mx-auto max-w-4xl px-4 pt-14 sm:px-6">
        <a href="{{ route('profile.index') }}" class="text-sm font-semibold text-laut hover:underline">Kembali ke profil</a>
        <h1 class="mt-6 text-4xl">Ajukan pembatalan</h1>
        <p class="mt-3 text-palung/70">Booking <strong class="text-palung">{{ $booking->booking_code }}</strong>. Pengajuan akan diperiksa staf sebelum disetujui.</p>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1.2fr_1fr]">
            <form method="POST" action="{{ route('cancellations.store', $booking) }}" class="panel" novalidate>
                @csrf

                <div class="rounded-xl {{ $refundPercentage > 0 ? 'bg-buih text-laut-tua' : 'bg-red-50 text-red-800' }} p-5">
                    <p class="text-sm">Perkiraan refund jika diajukan hari ini</p>
                    <p class="mt-1 font-display text-3xl">
                        {{ $refundPercentage }}%
                        <span class="text-xl">(Rp{{ number_format($refundAmount, 0, ',', '.') }})</span>
                    </p>
                    <p class="mt-2 text-sm leading-6">
                        {{ $booking->with_insurance ? 'Booking ini memakai asuransi.' : 'Booking ini tidak memakai asuransi.' }}
                        Check-in {{ $booking->check_in->translatedFormat('d F Y') }}.
                    </p>
                </div>

                <div class="mt-6">
                    <label for="reason" class="form-label">Alasan pembatalan</label>
                    <textarea id="reason" name="reason" rows="5" class="form-input" placeholder="Ceritakan singkat alasan pembatalan" required minlength="10">{{ old('reason') }}</textarea>
                    @error('reason') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('profile.index') }}" class="btn btn-secondary">Tidak jadi</a>
                    <button type="submit" class="btn btn-danger">Ajukan pembatalan</button>
                </div>
            </form>

            <aside class="panel lg:self-start">
                <h2 class="text-2xl">Rincian booking</h2>
                <div class="mt-6">
                    @include('partials.booking-summary', ['booking' => $booking])
                </div>
            </aside>
        </div>
    </section>
@endsection
