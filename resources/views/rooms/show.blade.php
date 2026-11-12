{{--
    Detail kamar - Fais
    Variabel dari RoomController@show:
        $room         : RoomType dengan relasi category
        $otherRooms   : RoomType lain (maks 3)
--}}
@extends('layouts.app')

@section('title', $room->name)
@section('description', \Illuminate\Support\Str::limit(strip_tags($room->description), 150))

@section('content')
    <div class="mx-auto max-w-6xl px-4 pt-10 sm:px-6">
        <nav aria-label="Breadcrumb" class="text-sm text-palung/60">
            <a href="{{ route('rooms.index') }}" class="hover:text-laut">Kamar</a>
            <span aria-hidden="true" class="mx-1.5">/</span>
            <a href="{{ route('rooms.index', ['kategori' => $room->category->slug]) }}" class="hover:text-laut">{{ $room->category->name }}</a>
        </nav>

        <div class="mt-6 grid gap-10 lg:grid-cols-[1.4fr_1fr]">
            <div>
                @include('partials.room-visual', ['room' => $room, 'class' => 'aspect-[16/10] rounded-2xl'])

                <h1 class="mt-8 text-4xl sm:text-5xl">{{ $room->name }}</h1>
                <p class="mt-4 max-w-2xl leading-8 text-palung/80">{{ $room->description }}</p>

                <h2 class="mt-10 text-2xl">Fasilitas</h2>
                <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ($room->facilities ?? [] as $facility)
                        <li class="flex items-start gap-3 text-palung/85">
                            <svg class="mt-1 size-4 shrink-0 text-ombak" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M3 8.5 6.5 12 13 4.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ $facility }}
                        </li>
                    @endforeach
                </ul>

                <h2 class="mt-10 text-2xl">Info menginap</h2>
                <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-3">
                    <div class="rounded-xl bg-buih p-4">
                        <dt class="text-palung/60">Check-in</dt>
                        <dd class="mt-1 text-lg font-semibold">Mulai {{ config('glamping.check_in_time') }}</dd>
                    </div>
                    <div class="rounded-xl bg-buih p-4">
                        <dt class="text-palung/60">Check-out</dt>
                        <dd class="mt-1 text-lg font-semibold">Sebelum {{ config('glamping.check_out_time') }}</dd>
                    </div>
                    <div class="rounded-xl bg-buih p-4">
                        <dt class="text-palung/60">Kapasitas</dt>
                        <dd class="mt-1 text-lg font-semibold">{{ $room->capacity }} orang/unit</dd>
                    </div>
                </dl>
            </div>

            {{-- Kotak pesan --}}
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <form method="GET" action="{{ route('booking.create') }}" class="panel">
                    <input type="hidden" name="kamar" value="{{ $room->slug }}">
                    <p>
                        <span class="font-display text-3xl">Rp{{ number_format($room->price_per_night, 0, ',', '.') }}</span>
                        <span class="text-palung/60">/unit/malam</span>
                    </p>
                    <p class="mt-1 text-sm text-palung/60">Total {{ $room->total_units }} unit tersedia di Bet Glamping.</p>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div>
                            <label for="check_in" class="form-label">Check-in</label>
                            <input type="date" id="check_in" name="check_in" min="{{ now()->toDateString() }}" class="form-input">
                        </div>
                        <div>
                            <label for="check_out" class="form-label">Check-out</label>
                            <input type="date" id="check_out" name="check_out" min="{{ now()->addDay()->toDateString() }}" class="form-input">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-5 w-full">Lanjut pesan</button>
                    <p class="mt-3 text-center text-xs text-palung/55">Kamu akan diminta masuk akun dulu.</p>

                    <div class="mt-6 rounded-xl bg-fajar/15 p-4 text-sm leading-6 text-fajar-tua">
                        Tambahkan asuransi pembatalan ({{ config('glamping.insurance_rate') }}%) saat reservasi untuk refund penuh jika batal lebih dari {{ config('glamping.refund_threshold_days') }} hari sebelum check-in.
                    </div>
                </form>
            </aside>
        </div>

        @if ($otherRooms->isNotEmpty())
            <section class="mt-24" aria-labelledby="judul-lain">
                <h2 id="judul-lain" class="text-3xl">Kamar lainnya</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($otherRooms as $other)
                        @include('partials.room-card', ['room' => $other])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
