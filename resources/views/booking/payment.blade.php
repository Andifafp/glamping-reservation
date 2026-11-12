{{--
    Pembayaran dummy - Hafiz
    Variabel: $booking (status pending) dengan relasi roomType
    Form dikirim ke route('booking.pay', $booking) dengan field: method
    Setelah "bayar": buat Payment status paid, ubah booking jadi confirmed,
    lalu redirect ke route('booking.success', $booking).
--}}
@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <section class="mx-auto max-w-5xl px-4 pt-12 sm:px-6">
        @include('partials.booking-steps', ['current' => 2])
        <h1 class="mt-6 text-4xl">Pembayaran</h1>
        <p class="mt-3 text-palung/70">
            Kode booking <strong class="font-semibold text-palung">{{ $booking->booking_code }}</strong>.
            Unit sudah kami tahan untukmu sampai pembayaran selesai.
        </p>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1.3fr_1fr]">
            <form method="POST" action="{{ route('booking.pay', $booking) }}" class="panel" novalidate>
                @csrf
                <fieldset>
                    <legend class="font-display text-2xl">Pilih metode</legend>
                    <div class="mt-6 space-y-3">
                        @foreach (config('glamping.payment_methods') as $value => $label)
                            <label class="flex cursor-pointer items-center gap-4 rounded-xl border border-palung/15 p-4 has-[:checked]:border-laut has-[:checked]:bg-buih">
                                <input type="radio" name="method" value="{{ $value }}" class="size-4 accent-laut" @checked(old('method', 'bank_transfer') === $value) required>
                                <span class="font-medium">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('method') <p class="form-error">{{ $message }}</p> @enderror
                </fieldset>

                <div class="mt-6 rounded-xl bg-fajar/15 p-4 text-sm leading-6 text-fajar-tua">
                    Ini pembayaran simulasi untuk keperluan tugas. Tidak ada uang yang ditarik.
                </div>

                <button type="submit" class="btn btn-primary mt-6 w-full">Bayar Rp{{ number_format($booking->total_price, 0, ',', '.') }}</button>
            </form>

            <aside class="panel lg:self-start">
                <h2 class="text-2xl">Rincian</h2>
                <div class="mt-6">
                    @include('partials.booking-summary', ['booking' => $booking])
                </div>
            </aside>
        </div>
    </section>
@endsection
