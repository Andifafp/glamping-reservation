{{-- Form tambah/ubah kategori - Fais. Variabel: $category --}}
@extends('layouts.dashboard')

@section('title', $category->exists ? 'Ubah kategori' : 'Tambah kategori')

@section('content')
    <form method="POST"
          action="{{ $category->exists ? route('dashboard.categories.update', $category) : route('dashboard.categories.store') }}"
          class="panel max-w-2xl space-y-5" novalidate>
        @csrf
        @if ($category->exists)
            @method('PUT')
        @endif

        <div>
            <label for="name" class="form-label">Nama kategori</label>
            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="form-input" placeholder="Safari Tent" required>
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="description" class="form-label">Deskripsi <span class="font-normal text-palung/50">(opsional)</span></label>
            <textarea id="description" name="description" rows="3" class="form-input">{{ old('description', $category->description) }}</textarea>
            @error('description') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
            <a href="{{ route('dashboard.categories.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">{{ $category->exists ? 'Simpan perubahan' : 'Tambah kategori' }}</button>
        </div>
    </form>
@endsection
