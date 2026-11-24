{{-- Daftar artikel di dashboard - Fais. Variabel: $articles (paginator), $status --}}
@extends('layouts.dashboard')

@section('title', 'Artikel')

@section('actions')
    <a href="{{ route('dashboard.articles.create') }}" class="btn btn-primary">Tulis artikel</a>
@endsection

@section('content')
    <nav class="flex flex-wrap gap-2" aria-label="Filter status">
        @foreach ([null => 'Semua', 'terbit' => 'Terbit', 'draf' => 'Draf'] as $value => $label)
            <a href="{{ route('dashboard.articles.index', array_filter(['status' => $value])) }}"
               class="rounded-full border px-4 py-2 text-sm font-medium {{ $status === ($value ?: null) ? 'border-laut bg-laut text-white' : 'border-palung/15 text-palung/75' }}">
                {{ $label }}
            </a>
        @endforeach
    </nav>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-palung/10 bg-white">
        <table class="w-full min-w-[44rem] text-left text-sm">
            <caption class="sr-only">Daftar artikel</caption>
            <thead class="border-b border-palung/10 text-palung/60">
                <tr>
                    <th scope="col" class="px-5 py-3 font-medium">Judul</th>
                    <th scope="col" class="px-5 py-3 font-medium">Status</th>
                    <th scope="col" class="px-5 py-3 font-medium">Penulis</th>
                    <th scope="col" class="px-5 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-palung/10">
                @forelse ($articles as $article)
                    <tr class="align-top">
                        <td class="px-5 py-4">
                            <p class="font-semibold">{{ $article->title }}</p>
                            <p class="mt-1 text-palung/60">Diubah {{ $article->updated_at->translatedFormat('d M Y, H:i') }}</p>
                        </td>
                        <td class="px-5 py-4">
                            @if ($article->is_published)
                                <span class="inline-flex rounded-full bg-buih px-2.5 py-1 text-xs font-semibold text-laut-tua">Terbit</span>
                            @else
                                <span class="inline-flex rounded-full bg-palung/10 px-2.5 py-1 text-xs font-semibold text-palung">Draf</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-palung/70">{{ $article->author?->name ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex justify-end gap-2">
                                @if ($article->is_published)
                                    <a href="{{ route('articles.show', $article) }}" class="btn px-3 py-1.5 text-laut hover:bg-buih" target="_blank" rel="noopener">Lihat</a>
                                @endif
                                <a href="{{ route('dashboard.articles.edit', $article) }}" class="btn btn-secondary px-3 py-1.5">Ubah</a>
                                <form method="POST" action="{{ route('dashboard.articles.destroy', $article) }}"
                                      onsubmit="return confirm('Hapus artikel ini? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn px-3 py-1.5 text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-palung/60">Belum ada artikel. Tulis artikel pertama.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $articles->links() }}</div>
@endsection
