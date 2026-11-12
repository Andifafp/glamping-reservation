@extends('layouts.app')

@section('title', 'Tidak punya akses')

@section('content')
    <section class="mx-auto max-w-xl px-4 pt-24 text-center sm:px-6">
        <p class="font-display text-7xl text-ombak/40">403</p>
        <h1 class="mt-4 text-3xl sm:text-4xl">Kamu tidak punya akses ke halaman ini</h1>
        <p class="mt-4 text-palung/70">Halaman ini khusus untuk akun dengan peran tertentu. Masuk dengan akun yang sesuai atau kembali ke beranda.</p>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke beranda</a>
            @guest
                <a href="{{ route('login') }}" class="btn btn-secondary">Masuk</a>
            @endguest
        </div>
    </section>
@endsection
