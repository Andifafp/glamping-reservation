{{--
    Daftar kamar - Fais
    Variabel dari RoomController@index:
        $categories     : semua Category (untuk filter)
        $rooms          : RoomType aktif (sudah difilter kategori)
        $activeCategory : slug kategori yang dipilih, atau null
        $checkIn, $checkOut : tanggal dari form cari (string Y-m-d) atau null
        Jika tanggal diisi, setiap $room punya atribut available_units.
--}}
@extends('layouts.app')

@section('title', 'Kamar')

@section('content')
    <section class="mx-auto max-w-6xl px-4 pt-14 sm:px-6">
        <h1 class="text-4xl sm:text-5xl">Kamar</h1>
        <p class="mt-4 max-w-xl text-palung/70">
            Semua harga per unit per malam. Isi tanggal untuk melihat sisa unit di tanggal tersebut.
        </p>

        {{-- Cari tanggal --}}
        <form method="GET" action="{{ route('rooms.index') }}" class="mt-8 grid gap-4 rounded-2xl border border-palung/10 bg-white p-5 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
            @if ($activeCategory)
                <input type="hidden" name="kategori" value="{{ $activeCategory }}">
            @endif
            <div>
                <label for="check_in" class="form-label">Check-in</label>
                <input type="date" id="check_in" name="check_in" value="{{ $checkIn ?? request('check_in') }}" min="{{ now()->toDateString() }}" class="form-input">
                @error('check_in') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="check_out" class="form-label">Check-out</label>
                <input type="date" id="check_out" name="check_out" value="{{ $checkOut ?? request('check_out') }}" class="form-input">
                @error('check_out') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn btn-primary h-[42px]">Cek ketersediaan</button>
        </form>

        {{-- Filter kategori --}}
        <nav class="mt-8 flex flex-wrap gap-2" aria-label="Filter kategori">
            @php $dateQuery = array_filter(['check_in' => $checkIn, 'check_out' => $checkOut]); @endphp
            <a href="{{ route('rooms.index', $dateQuery) }}"
               class="rounded-full border px-4 py-2 text-sm font-medium {{ $activeCategory ? 'border-palung/15 text-palung/75 hover:border-laut hover:text-laut' : 'border-laut bg-laut text-white' }}"
               @unless ($activeCategory) aria-current="page" @endunless>
                Semua
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('rooms.index', $dateQuery + ['kategori' => $category->slug]) }}"
                   class="rounded-full border px-4 py-2 text-sm font-medium {{ $activeCategory === $category->slug ? 'border-laut bg-laut text-white' : 'border-palung/15 text-palung/75 hover:border-laut hover:text-laut' }}"
                   @if ($activeCategory === $category->slug) aria-current="page" @endif>
                    {{ $category->name }}
                </a>
            @endforeach
        </nav>

        @if ($checkIn && $checkOut)
            <p class="mt-6 text-sm text-palung/70" role="status">
                Ketersediaan untuk
                <strong class="text-palung">{{ \Carbon\Carbon::parse($checkIn)->translatedFormat('d M Y') }}</strong>
                sampai
                <strong class="text-palung">{{ \Carbon\Carbon::parse($checkOut)->translatedFormat('d M Y') }}</strong>.
            </p>
        @endif

        {{-- Daftar --}}
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($rooms as $room)
                <div class="flex flex-col">
                    @include('partials.room-card', ['room' => $room])
                    @isset($room->available_units)
                        <p class="mt-2 px-1 text-sm {{ $room->available_units > 0 ? 'text-laut' : 'text-red-700' }}">
                            @if ($room->available_units > 0)
                                Sisa {{ $room->available_units }} dari {{ $room->total_units }} unit
                            @else
                                Penuh di tanggal ini
                            @endif
                        </p>
                    @endisset
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-palung/20 p-10 text-center sm:col-span-2 lg:col-span-3">
                    <p class="font-semibold">Belum ada kamar di kategori ini.</p>
                    <a href="{{ route('rooms.index') }}" class="mt-3 inline-block text-sm font-semibold text-laut hover:underline">Lihat semua kamar</a>
                </div>
            @endforelse
        </div>
    </section>
@endsection
