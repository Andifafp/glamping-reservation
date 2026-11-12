{{--
    Reservasi berhasil - Hafiz
    Variabel: $booking (status confirmed) dengan relasi roomType
--}}
@extends('layouts.app')

@section('title', 'Reservasi berhasil')

@section('content')
    <section class="mx-auto max-w-2xl px-4 pt-12 sm:px-6">
        @include('partials.booking-steps', ['current' => 3])

        <div class="panel mt-8 text-center">
            <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-buih text-laut" aria-hidden="true">
                <svg class="size-7" viewBox="0 0 24 24" fill="none">
                    <path d="M5 12.5 9.5 17 19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h1 class="mt-5 text-4xl">Reservasi terkonfirmasi</h1>
            <p class="mt-3 text-palung/70">Simpan kode booking ini. Kamu memerlukannya saat check-in.</p>

            <p class="mt-6 inline-block rounded-xl border-2 border-dashed border-laut/40 px-6 py-3 font-display text-3xl tracking-wide text-laut">
                {{ $booking->booking_code }}
            </p>

            <div class="mt-8 text-left">
                @include('partials.booking-summary', ['booking' => $booking])
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                <a href="{{ route('booking.check', ['kode' => $booking->booking_code]) }}" class="btn btn-primary">Lihat kartu check-in</a>
                <a href="{{ route('profile.index') }}" class="btn btn-secondary">Ke profil saya</a>
            </div>
        </div>
    </section>
@endsection
