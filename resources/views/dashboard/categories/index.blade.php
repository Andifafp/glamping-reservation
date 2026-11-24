{{-- Daftar kategori - Fais. Variabel: $categories (dengan room_types_count) --}}
@extends('layouts.dashboard')

@section('title', 'Kategori')

@section('actions')
    <a href="{{ route('dashboard.categories.create') }}" class="btn btn-primary">Tambah kategori</a>
@endsection

@section('content')
    <div class="overflow-x-auto rounded-2xl border border-palung/10 bg-white">
        <table class="w-full min-w-[36rem] text-left text-sm">
            <caption class="sr-only">Daftar kategori kamar</caption>
            <thead class="border-b border-palung/10 text-palung/60">
                <tr>
                    <th scope="col" class="px-5 py-3 font-medium">Kategori</th>
                    <th scope="col" class="px-5 py-3 font-medium">Jenis kamar</th>
                    <th scope="col" class="px-5 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-palung/10">
                @forelse ($categories as $category)
                    <tr class="align-top">
                        <td class="px-5 py-4">
                            <p class="font-semibold">{{ $category->name }}</p>
                            <p class="mt-1 max-w-md text-palung/60">{{ $category->description }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <a href="{{ route('dashboard.room-types.index', ['kategori' => $category->id]) }}" class="text-laut hover:underline">
                                {{ $category->room_types_count }} jenis
                            </a>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('dashboard.categories.edit', $category) }}" class="btn btn-secondary px-3 py-1.5">Ubah</a>
                                <form method="POST" action="{{ route('dashboard.categories.destroy', $category) }}"
                                      onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn px-3 py-1.5 text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-10 text-center text-palung/60">Belum ada kategori. Tambahkan kategori pertama.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
