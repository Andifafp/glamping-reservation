{{--
    Beranda (landing page) - Fais
    Variabel dari HomeController:
        $categories    : Category, dengan rooms_count dan min_price
        $featuredRooms : RoomType (maks 4), dengan relasi category
        $articles      : Article terbit terbaru (maks 3)
--}}
@extends('layouts.app')

@section('title', 'Glamping tepi laut')

@section('content')
    {{-- Hero --}}
    <section class="relative isolate overflow-hidden bg-palung text-white">
        @include('partials.hero-scene')
        <div class="absolute inset-0 bg-gradient-to-r from-palung/85 via-palung/40 to-transparent" aria-hidden="true"></div>

        <div class="relative mx-auto max-w-6xl px-4 pt-20 pb-40 sm:px-6 sm:pt-28 sm:pb-48">
            <h1 class="max-w-xl text-4xl leading-[1.1] sm:text-6xl">Tidur di tebing, bangun disambut laut.</h1>
            <p class="mt-6 max-w-md text-base leading-7 text-white/80 sm:text-lg">
                Tenda safari, kabin kayu, dan dome menghadap Samudra Hindia. Pilih tanggal, lihat unit yang masih kosong, lalu pesan dalam beberapa menit.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('rooms.index') }}" class="btn btn-light">Lihat kamar</a>
                <a href="{{ route('booking.check') }}" class="btn border border-white/40 text-white hover:bg-white/10">Cek booking</a>
            </div>
        </div>
    </section>

    {{-- Cari ketersediaan --}}
    <section class="relative z-10 mx-auto -mt-24 max-w-6xl px-4 sm:px-6" aria-labelledby="judul-cari">
        <form method="GET" action="{{ route('rooms.index') }}" class="panel grid gap-4 shadow-xl shadow-palung/10 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_0.7fr_auto] lg:items-end">
            <h2 id="judul-cari" class="font-sans text-base font-semibold sm:col-span-2 lg:col-span-4">Cek kamar yang masih kosong</h2>
            <div>
                <label for="cari-check-in" class="form-label">Check-in</label>
                <input type="date" id="cari-check-in" name="check_in" class="form-input" min="{{ now()->toDateString() }}" required>
            </div>
            <div>
                <label for="cari-check-out" class="form-label">Check-out</label>
                <input type="date" id="cari-check-out" name="check_out" class="form-input" min="{{ now()->addDay()->toDateString() }}" required>
            </div>
            <div>
                <label for="cari-tamu" class="form-label">Tamu</label>
                <input type="number" id="cari-tamu" name="tamu" class="form-input" min="1" max="20" value="2">
            </div>
            <button type="submit" class="btn btn-primary h-[42px]">Cari kamar</button>
        </form>
    </section>

    {{-- Kategori --}}
    <section class="mx-auto max-w-6xl px-4 pt-24 sm:px-6" aria-labelledby="judul-kategori">
        <h2 id="judul-kategori" class="text-3xl sm:text-4xl">Pilih cara menginap</h2>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($categories as $category)
                <a href="{{ route('rooms.index', ['kategori' => $category->slug]) }}"
                   class="group flex flex-col overflow-hidden rounded-2xl bg-palung text-white">
                    @if ($category->sample_room)
                        <div class="relative" aria-hidden="true">
                            @include('partials.room-visual', ['room' => $category->sample_room, 'class' => 'aspect-[16/9] transition-transform duration-500 group-hover:scale-[1.03]'])
                        </div>
                    @endif
                    <div class="flex flex-1 flex-col p-6">
                        <h3 class="text-2xl">{{ $category->name }}</h3>
                        <p class="mt-2 text-sm leading-6 text-white/80">{{ $category->description }}</p>
                        <p class="mt-auto pt-4 text-sm">
                            {{ $category->rooms_count }} jenis kamar, mulai
                            <span class="font-semibold text-fajar">Rp{{ number_format($category->min_price, 0, ',', '.') }}</span>/malam
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Kamar --}}
    <section class="mx-auto max-w-6xl px-4 pt-24 sm:px-6" aria-labelledby="judul-kamar">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 id="judul-kamar" class="text-3xl sm:text-4xl">Semua kamar menghadap laut</h2>
                <p class="mt-3 max-w-lg text-palung/70">Harga per unit per malam, sudah termasuk sarapan dan pajak.</p>
            </div>
            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Lihat semua kamar</a>
        </div>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($featuredRooms as $room)
                @include('partials.room-card', ['room' => $room])
            @endforeach
        </div>
    </section>

    {{-- Fasilitas --}}
    <section class="mt-24 bg-buih" aria-labelledby="judul-fasilitas">
        <div class="mx-auto grid max-w-6xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-[1fr_1.2fr]">
            <div>
                <h2 id="judul-fasilitas" class="text-3xl sm:text-4xl">Yang sudah termasuk di setiap kamar</h2>
                <p class="mt-4 max-w-md leading-7 text-palung/75">
                    Tidak perlu membawa tenda, matras, atau kompor. Datang dengan satu tas, sisanya kami siapkan.
                </p>
            </div>
            <dl class="grid gap-x-10 gap-y-8 sm:grid-cols-2">
                @foreach ([
                    ['Kasur sungguhan', 'Kasur queen atau king dengan seprai bersih, bukan sleeping bag.'],
                    ['Kamar mandi dalam', 'Air panas tersedia 24 jam di setiap unit.'],
                    ['Sarapan', 'Disajikan di teras kamar mulai pukul 07.00.'],
                    ['Api unggun', 'Kayu bakar disiapkan staf setiap sore, gratis.'],
                ] as [$judul, $isi])
                    <div class="border-l-2 border-ombak pl-5">
                        <dt class="font-semibold">{{ $judul }}</dt>
                        <dd class="mt-1.5 text-sm leading-6 text-palung/70">{{ $isi }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- Asuransi dan refund --}}
    <section id="asuransi" class="scroll-mt-20 bg-palung text-white" aria-labelledby="judul-asuransi">
        <div class="mx-auto grid max-w-6xl gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:items-center">
            <div>
                <h2 id="judul-asuransi" class="text-3xl sm:text-4xl">Rencana bisa berubah. Asuransi pembatalan menjaga uangmu.</h2>
                <p class="mt-5 max-w-md leading-7 text-white/75">
                    Centang asuransi saat reservasi dengan biaya tambahan
                    <span class="font-semibold text-fajar">{{ config('glamping.insurance_rate') }}% dari harga kamar</span>.
                    Kalau batal, besar refund dihitung dari jarak hari pengajuan ke tanggal check-in.
                </p>
            </div>

            <div class="overflow-hidden rounded-2xl border border-white/15">
                <table class="w-full text-left text-sm">
                    <caption class="sr-only">Persentase refund pembatalan</caption>
                    <thead class="bg-white/5 text-white/70">
                        <tr>
                            <th scope="col" class="px-5 py-4 font-medium">Jenis booking</th>
                            <th scope="col" class="px-5 py-4 font-medium">Batal lebih dari {{ config('glamping.refund_threshold_days') }} hari sebelum check-in</th>
                            <th scope="col" class="px-5 py-4 font-medium">Batal {{ config('glamping.refund_threshold_days') }} hari atau kurang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <tr>
                            <th scope="row" class="px-5 py-5 font-semibold">Dengan asuransi</th>
                            <td class="px-5 py-5 font-display text-3xl text-fajar">{{ config('glamping.refund.with_insurance.before_threshold') }}%</td>
                            <td class="px-5 py-5 font-display text-3xl">{{ config('glamping.refund.with_insurance.after_threshold') }}%</td>
                        </tr>
                        <tr>
                            <th scope="row" class="px-5 py-5 font-semibold">Tanpa asuransi</th>
                            <td class="px-5 py-5 font-display text-3xl">{{ config('glamping.refund.without_insurance.before_threshold') }}%</td>
                            <td class="px-5 py-5 font-display text-3xl text-white/50">{{ config('glamping.refund.without_insurance.after_threshold') }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Cara reservasi --}}
    <section id="cara-reservasi" class="mx-auto max-w-6xl scroll-mt-20 px-4 pt-24 sm:px-6" aria-labelledby="judul-cara">
        <h2 id="judul-cara" class="text-3xl sm:text-4xl">Cara reservasi</h2>
        <ol class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Masuk atau daftar', 'Reservasi membutuhkan akun, supaya riwayat dan pembatalan bisa kamu pantau di profil.'],
                ['Pilih kamar dan tanggal', 'Sistem langsung mengecek apakah unit masih kosong di tanggal itu.'],
                ['Bayar', 'Pilih metode pembayaran. Di situs ini pembayarannya simulasi.'],
                ['Tunjukkan kode booking', 'Saat tiba, sebutkan kode booking atau tunjukkan kartu check-in.'],
            ] as $i => [$judul, $isi])
                <li class="relative pt-12">
                    <span class="absolute top-0 left-0 font-display text-4xl text-ombak/40" aria-hidden="true">{{ $i + 1 }}</span>
                    <h3 class="font-sans text-base font-semibold">{{ $judul }}</h3>
                    <p class="mt-2 text-sm leading-6 text-palung/70">{{ $isi }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Artikel --}}
    @if ($articles->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 pt-24 sm:px-6" aria-labelledby="judul-artikel">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <h2 id="judul-artikel" class="text-3xl sm:text-4xl">Bacaan sebelum berangkat</h2>
                <a href="{{ route('articles.index') }}" class="text-sm font-semibold text-laut hover:underline">Semua artikel</a>
            </div>
            <div class="mt-10 grid gap-10 md:grid-cols-3">
                @foreach ($articles as $article)
                    @include('partials.article-card', ['article' => $article])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Ajakan cek booking --}}
    <section class="mx-auto max-w-6xl px-4 pt-24 sm:px-6">
        <div class="flex flex-col items-start justify-between gap-6 rounded-2xl bg-laut px-6 py-10 text-white sm:flex-row sm:items-center sm:px-10">
            <div>
                <h2 class="text-2xl sm:text-3xl">Sudah punya kode booking?</h2>
                <p class="mt-2 text-white/75">Lihat status reservasi dan kartu check-in tanpa perlu masuk akun.</p>
            </div>
            <a href="{{ route('booking.check') }}" class="btn btn-light shrink-0">Cek booking</a>
        </div>
    </section>
@endsection
