{{--
    Halaman masuk - Marcel
    Form dikirim ke route('login.attempt') dengan field: email, password, remember.
    Setelah berhasil: redirect()->intended(route('home'))
    supaya pengunjung yang tadi klik "Pesan" kembali ke form reservasi.
--}}
@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <section class="mx-auto grid max-w-5xl gap-10 px-4 pt-14 sm:px-6 lg:grid-cols-2 lg:items-center">
        <div class="hidden lg:block">
            <div class="relative aspect-[4/5] overflow-hidden rounded-3xl bg-palung">
                @include('partials.hero-scene')
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-palung to-transparent p-8 pt-24 text-white">
                    <p class="font-display text-2xl leading-snug">Riwayat menginap, status pembatalan, dan kartu check-in ada di satu akun.</p>
                </div>
            </div>
        </div>

        <div class="panel mx-auto w-full max-w-md">
            <h1 class="text-3xl">Masuk</h1>
            <p class="mt-2 text-sm text-palung/70">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-laut hover:underline">Daftar dulu</a>
            </p>

            <form method="POST" action="{{ route('login.attempt') }}" class="mt-8 space-y-5" novalidate>
                @csrf
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-input" autocomplete="email" required autofocus
                           @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
                    @error('email') <p id="email-error" class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-input" autocomplete="current-password" required
                           @error('password') aria-invalid="true" aria-describedby="password-error" @enderror>
                    @error('password') <p id="password-error" class="form-error">{{ $message }}</p> @enderror
                </div>
                <label class="flex items-center gap-2 text-sm text-palung/80">
                    <input type="checkbox" name="remember" value="1" class="size-4 rounded border-palung/30 accent-laut" @checked(old('remember'))>
                    Ingat saya di perangkat ini
                </label>
                <button type="submit" class="btn btn-primary w-full">Masuk</button>
            </form>
        </div>
    </section>
@endsection
