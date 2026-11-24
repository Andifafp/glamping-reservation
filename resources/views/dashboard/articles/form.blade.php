{{-- Form tulis/ubah artikel - Fais. Variabel: $article --}}
@extends('layouts.dashboard')

@section('title', $article->exists ? 'Ubah artikel' : 'Tulis artikel')

@section('content')
    <form method="POST" enctype="multipart/form-data"
          action="{{ $article->exists ? route('dashboard.articles.update', $article) : route('dashboard.articles.store') }}"
          class="panel max-w-3xl space-y-5" novalidate>
        @csrf
        @if ($article->exists)
            @method('PUT')
        @endif

        <div>
            <label for="title" class="form-label">Judul</label>
            <input type="text" id="title" name="title" value="{{ old('title', $article->title) }}" class="form-input" required>
            @error('title') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="excerpt" class="form-label">Ringkasan <span class="font-normal text-palung/50">(tampil di kartu artikel, maks 300 karakter)</span></label>
            <textarea id="excerpt" name="excerpt" rows="2" maxlength="300" class="form-input">{{ old('excerpt', $article->excerpt) }}</textarea>
            @error('excerpt') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="content" class="form-label">Isi artikel</label>
            <textarea id="content" name="content" rows="14" class="form-input font-mono text-[13px] leading-6" aria-describedby="content-hint" required>{{ old('content', $article->content) }}</textarea>
            <p id="content-hint" class="form-hint">
                Tulis biasa (baris kosong = paragraf baru), atau pakai tag
                <code>&lt;h2&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul&gt;&lt;li&gt;</code>, <code>&lt;strong&gt;</code>.
                Tag lain dan atribut dibuang otomatis.
            </p>
            @error('content') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="thumbnail" class="form-label">Gambar <span class="font-normal text-palung/50">(opsional, maks 2 MB)</span></label>
            @if ($article->thumbnail)
                <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="Gambar artikel saat ini" class="mb-3 h-24 rounded-lg object-cover">
            @endif
            <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/webp" class="form-input">
            @error('thumbnail') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <label class="flex items-center gap-3 text-sm font-medium">
            <input type="checkbox" name="is_published" value="1" class="size-4 accent-laut" @checked(old('is_published', $article->is_published))>
            Terbitkan (tampil di halaman Artikel)
        </label>

        <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
            <a href="{{ route('dashboard.articles.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan artikel</button>
        </div>
    </form>
@endsection
