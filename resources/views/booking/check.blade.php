{{--
    Cek booking + kartu check-in - Hafiz
    Bisa diakses tanpa login.
    Variabel:
        $booking : null saat pertama dibuka; berisi Booking jika kode + kontak cocok
    Form dikirim ke route('booking.lookup') dengan field: booking_code, contact
    (contact = email ATAU nomor HP pemesan)
    Kartu check-in hanya tampil jika status booking 'confirmed'.
--}}
@extends('layouts.app')

@section('title', 'Cek booking')

@section('content')
    <section class="mx-auto max-w-3xl px-4 pt-14 sm:px-6 print:pt-0">
        <div class="print:hidden">
            <h1 class="text-4xl sm:text-5xl">Cek booking</h1>
            <p class="mt-4 max-w-lg text-palung/70">Masukkan kode booking dan email atau nomor HP yang dipakai saat reservasi.</p>

            <form method="POST" action="{{ route('booking.lookup') }}" class="mt-8 grid gap-4 rounded-2xl border border-palung/10 bg-white p-5 sm:grid-cols-[1fr_1.3fr_auto] sm:items-end" novalidate>
                @csrf
                <div>
                    <label for="booking_code" class="form-label">Kode booking</label>
                    <input type="text" id="booking_code" name="booking_code" value="{{ old('booking_code', request('kode', $booking?->booking_code)) }}"
                           class="form-input uppercase" placeholder="GLM-8K2P4Q" autocomplete="off" required>
                    @error('booking_code') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="contact" class="form-label">Email atau nomor HP</label>
                    <input type="text" id="contact" name="contact" value="{{ old('contact') }}" class="form-input" placeholder="nama@email.com" required>
                    @error('contact') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn btn-primary h-[42px]">Cek</button>
            </form>
        </div>

        @if ($booking)
            @if ($booking->status === 'confirmed')
                {{-- Kartu check-in --}}
                <article class="mt-10 overflow-hidden rounded-3xl border border-palung/10 bg-white print:mt-0 print:border-palung/30" aria-labelledby="judul-kartu">
                    <header class="flex items-center justify-between gap-4 bg-palung px-6 py-5 text-white sm:px-8">
                        <div class="flex items-center gap-3">
                            @include('partials.logo', ['size' => 30])
                            <h2 id="judul-kartu" class="font-sans text-base font-semibold">Kartu check-in</h2>
                        </div>
                        @include('partials.status-badge', ['status' => $booking->status])
                    </header>

                    <div class="grid gap-8 p-6 sm:grid-cols-[1fr_auto] sm:p-8">
                        <div>
                            <p class="text-sm text-palung/60">Kode booking</p>
                            <p class="font-display text-4xl text-laut">{{ $booking->booking_code }}</p>

                            <dl class="mt-6 grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                                <div class="col-span-2">
                                    <dt class="text-palung/60">Nama tamu</dt>
                                    <dd class="font-semibold">{{ $booking->contact_name }}</dd>
                                </div>
                                <div class="col-span-2">
                                    <dt class="text-palung/60">Kamar</dt>
                                    <dd class="font-semibold">{{ $booking->roomType->name }}, {{ $booking->units }} unit</dd>
                                </div>
                                <div>
                                    <dt class="text-palung/60">Check-in</dt>
                                    <dd class="font-semibold">{{ $booking->check_in->translatedFormat('D, d M Y') }}</dd>
                                    <dd class="text-palung/60">mulai {{ config('glamping.check_in_time') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-palung/60">Check-out</dt>
                                    <dd class="font-semibold">{{ $booking->check_out->translatedFormat('D, d M Y') }}</dd>
                                    <dd class="text-palung/60">sebelum {{ config('glamping.check_out_time') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-palung/60">Tamu</dt>
                                    <dd class="font-semibold">{{ $booking->guests }} orang</dd>
                                </div>
                                <div>
                                    <dt class="text-palung/60">Asuransi</dt>
                                    <dd class="font-semibold">{{ $booking->with_insurance ? 'Ya' : 'Tidak' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="mx-auto w-40 sm:w-44">
                            <div class="aspect-square rounded-xl border border-palung/10 p-2">
                                @include('partials.qr-dummy', ['code' => $booking->booking_code])
                            </div>
                            <p class="mt-2 text-center text-xs text-palung/55">QR dummy, tunjukkan ke resepsionis</p>
                        </div>
                    </div>

                    {{-- Checklist serah terima kamar --}}
                    <div class="border-t border-dashed border-palung/20 p-6 sm:p-8">
                        <h3 class="font-sans text-base font-semibold">Checklist serah terima kamar</h3>
                        <p class="mt-1 text-sm text-palung/60">Diperiksa bersama staf saat check-in dan check-out.</p>
                        <ul class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
                            @foreach ([
                                'Kunci atau kartu akses kamar',
                                'Kasur, bantal, dan selimut lengkap',
                                'Lampu dan stop kontak berfungsi',
                                'Air panas kamar mandi menyala',
                                'Handuk sesuai jumlah tamu',
                                'Peralatan api unggun (jika ada)',
                            ] as $item)
                                <li class="flex items-center gap-3">
                                    <span class="size-5 shrink-0 rounded border-2 border-palung/30" aria-hidden="true"></span>
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </article>

                <div class="mt-6 flex justify-end print:hidden">
                    <button type="button" onclick="window.print()" class="btn btn-secondary">Cetak kartu</button>
                </div>
            @else
                <div class="mt-10 rounded-2xl border border-palung/10 bg-white p-6" role="status">
                    <div class="flex flex-wrap items-center gap-3">
                        <p class="font-display text-2xl">{{ $booking->booking_code }}</p>
                        @include('partials.status-badge', ['status' => $booking->status])
                    </div>
                    <p class="mt-3 text-palung/75">
                        @switch($booking->status)
                            @case('pending')
                                Booking ini belum dibayar. Kartu check-in muncul setelah pembayaran selesai.
                                @break
                            @case('cancelled')
                                Booking ini sudah dibatalkan, jadi tidak bisa dipakai untuk check-in.
                                @break
                            @default
                                Masa menginap untuk booking ini sudah selesai.
                        @endswitch
                    </p>
                </div>
            @endif
        @endif
    </section>
@endsection
