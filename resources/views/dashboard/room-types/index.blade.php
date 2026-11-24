{{--
    Daftar jenis kamar - Fais
    Variabel: $rooms (dengan category, active_bookings_count), $categories, $categoryId
--}}
@extends('layouts.dashboard')

@section('title', 'Jenis kamar')

@section('actions')
    <a href="{{ route('dashboard.room-types.create') }}" class="btn btn-primary">Tambah jenis kamar</a>
@endsection

@section('content')
    <nav class="flex flex-wrap gap-2" aria-label="Filter kategori">
        <a href="{{ route('dashboard.room-types.index') }}"
           class="rounded-full border px-4 py-2 text-sm font-medium {{ $categoryId ? 'border-palung/15 text-palung/75' : 'border-laut bg-laut text-white' }}">Semua</a>
        @foreach ($categories as $category)
            <a href="{{ route('dashboard.room-types.index', ['kategori' => $category->id]) }}"
               class="rounded-full border px-4 py-2 text-sm font-medium {{ $categoryId === $category->id ? 'border-laut bg-laut text-white' : 'border-palung/15 text-palung/75' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </nav>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-palung/10 bg-white">
        <table class="w-full min-w-[52rem] text-left text-sm">
            <caption class="sr-only">Daftar jenis kamar</caption>
            <thead class="border-b border-palung/10 text-palung/60">
                <tr>
                    <th scope="col" class="px-5 py-3 font-medium">Kamar</th>
                    <th scope="col" class="px-5 py-3 font-medium">Kapasitas</th>
                    <th scope="col" class="px-5 py-3 font-medium">Stok unit</th>
                    <th scope="col" class="px-5 py-3 font-medium">Tarif/malam</th>
                    <th scope="col" class="px-5 py-3 font-medium">Status</th>
                    <th scope="col" class="px-5 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-palung/10">
                @forelse ($rooms as $room)
                    <tr class="align-top">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-16 shrink-0 overflow-hidden rounded-lg">
                                    @include('partials.room-visual', ['room' => $room, 'class' => 'aspect-[4/3]'])
                                </div>
                                <div>
                                    <p class="font-semibold">{{ $room->name }}</p>
                                    <p class="text-palung/60">{{ $room->category->name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">{{ $room->capacity }} orang</td>
                        <td class="px-5 py-4">
                            <p class="font-semibold">{{ $room->total_units }} unit</p>
                            <p class="text-palung/60">{{ $room->active_bookings_count }} booking aktif</p>
                        </td>
                        <td class="px-5 py-4">Rp{{ number_format($room->price_per_night, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            @if ($room->is_active)
                                <span class="text-laut">Tampil</span>
                            @else
                                <span class="text-red-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('dashboard.room-types.edit', $room) }}" class="btn btn-secondary px-3 py-1.5">Ubah</a>
                                <form method="POST" action="{{ route('dashboard.room-types.destroy', $room) }}"
                                      onsubmit="return confirm('Hapus {{ $room->name }}? Jika sudah pernah dipesan, kamar hanya dinonaktifkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn px-3 py-1.5 text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-palung/60">Belum ada jenis kamar di kategori ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
