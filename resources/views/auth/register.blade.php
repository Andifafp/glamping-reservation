{{--
    Halaman daftar - Marcel
    Form dikirim ke route('register.store') dengan field:
        name, email, phone, password, password_confirmation
    Akun baru selalu role 'user'. Jangan ambil role dari input form.
--}}
@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <section class="mx-auto max-w-md px-4 pt-14 sm:px-6">
        <div class="panel">
            <h1 class="text-3xl">Buat akun</h1>
            <p class="mt-2 text-sm text-palung/70">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-laut hover:underline">Masuk</a>
            </p>

            <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5" novalidate>
                @csrf
                <div>
                    <label for="name" class="form-label">Nama lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input" autocomplete="name" required
                           @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                    @error('name') <p id="name-error" class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-input" autocomplete="email" placeholder="nama@email.com" required
                           @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email') <p id="email-error" class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="phone" class="form-label">Nomor HP</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-input" autocomplete="tel" placeholder="081234567890" required
                           @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
                    @error('phone') <p id="phone-error" class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" autocomplete="new-password" aria-describedby="password-hint" required>
                    <p id="password-hint" class="form-hint">Minimal 8 karakter.</p>
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="form-label">Ulangi password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary w-full">Daftar</button>
            </form>
        </div>
    </section>
@endsection
