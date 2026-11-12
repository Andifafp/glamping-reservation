{{--
    Edit data diri - Thoni
    Variabel: $user
    Form dikirim ke route('profile.update') (PUT) dengan field:
        name, email, phone, current_password, password, password_confirmation
    Password hanya diganti jika kolom password diisi. Wajib cek current_password.
--}}
@extends('layouts.app')

@section('title', 'Ubah data diri')

@section('content')
    <section class="mx-auto max-w-xl px-4 pt-14 sm:px-6">
        <a href="{{ route('profile.index') }}" class="text-sm font-semibold text-laut hover:underline">Kembali ke profil</a>
        <h1 class="mt-6 text-4xl">Ubah data diri</h1>

        <form method="POST" action="{{ route('profile.update') }}" class="panel mt-8 space-y-6" novalidate>
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="form-label">Nama lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input" autocomplete="name" required>
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" autocomplete="email" required>
                @error('email') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="phone" class="form-label">Nomor HP</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" autocomplete="tel" required>
                @error('phone') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <fieldset class="border-t border-palung/10 pt-6">
                <legend class="sr-only">Ganti password</legend>
                <h2 class="font-sans text-base font-semibold">Ganti password</h2>
                <p class="mt-1 text-sm text-palung/60">Kosongkan jika tidak ingin mengganti.</p>
                <div class="mt-5 space-y-5">
                    <div>
                        <label for="current_password" class="form-label">Password sekarang</label>
                        <input type="password" id="current_password" name="current_password" class="form-input" autocomplete="current-password">
                        @error('current_password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password" class="form-label">Password baru</label>
                        <input type="password" id="password" name="password" class="form-input" autocomplete="new-password">
                        @error('password') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="form-label">Ulangi password baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" autocomplete="new-password">
                    </div>
                </div>
            </fieldset>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('profile.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan perubahan</button>
            </div>
        </form>
    </section>
@endsection
