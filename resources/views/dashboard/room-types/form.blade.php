{{-- Form tambah/ubah jenis kamar - Fais. Variabel: $room, $categories --}}
@extends('layouts.dashboard')

@section('title', $room->exists ? 'Ubah jenis kamar' : 'Tambah jenis kamar')

@section('content')
    <form method="POST" enctype="multipart/form-data"
          action="{{ $room->exists ? route('dashboard.room-types.update', $room) : route('dashboard.room-types.store') }}"
          class="panel max-w-3xl" novalidate>
        @csrf
        @if ($room->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="name" class="form-label">Nama kamar</label>
                <input type="text" id="name" name="name" value="{{ old('name', $room->name) }}" class="form-input" placeholder="Safari Tent Standard" required>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="category_id" class="form-label">Kategori</label>
                <select id="category_id" name="category_id" class="form-input" required>
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $room->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="capacity" class="form-label">Kapasitas per unit (orang)</label>
                <input type="number" id="capacity" name="capacity" min="1" max="20" value="{{ old('capacity', $room->capacity) }}" class="form-input" required>
                @error('capacity') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="total_units" class="form-label">Stok unit</label>
                <input type="number" id="total_units" name="total_units" min="0" max="500" value="{{ old('total_units', $room->total_units) }}" class="form-input" aria-describedby="units-hint" required>
                <p id="units-hint" class="form-hint">Jumlah unit fisik yang bisa disewa bersamaan.</p>
                @error('total_units') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="price_per_night" class="form-label">Tarif per malam (Rp)</label>
                <input type="number" id="price_per_night" name="price_per_night" min="10000" step="1000" value="{{ old('price_per_night', $room->price_per_night) }}" class="form-input" placeholder="650000" required>
                @error('price_per_night') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea id="description" name="description" rows="3" class="form-input">{{ old('description', $room->description) }}</textarea>
                @error('description') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="facilities" class="form-label">Fasilitas</label>
                <textarea id="facilities" name="facilities" rows="5" class="form-input" aria-describedby="facilities-hint"
                          placeholder="Kasur queen&#10;Kamar mandi dalam&#10;Sarapan 2 orang">{{ old('facilities', implode("\n", $room->facilities ?? [])) }}</textarea>
                <p id="facilities-hint" class="form-hint">Satu fasilitas per baris.</p>
                @error('facilities') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label for="image" class="form-label">Foto <span class="font-normal text-palung/50">(opsional, JPG/PNG/WebP, maks 2 MB)</span></label>
                @if ($room->image)
                    <div class="mb-3 flex items-center gap-4">
                        <img src="{{ asset('storage/' . $room->image) }}" alt="Foto {{ $room->name }} saat ini" class="h-20 w-28 rounded-lg object-cover">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="remove_image" value="1" class="size-4 accent-laut">
                            Hapus foto ini
                        </label>
                    </div>
                @endif
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" class="form-input">
                @error('image') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div class="sm:col-span-2">
                <label class="flex items-center gap-3 text-sm font-medium">
                    <input type="checkbox" name="is_active" value="1" class="size-4 accent-laut" @checked(old('is_active', $room->is_active))>
                    Tampilkan di situs (bisa dipesan)
                </label>
            </div>
        </div>

        <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('dashboard.room-types.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">{{ $room->exists ? 'Simpan perubahan' : 'Tambah jenis kamar' }}</button>
        </div>
    </form>
@endsection
