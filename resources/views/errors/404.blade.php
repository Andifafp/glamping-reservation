@extends('layouts.app')

@section('title', 'Halaman tidak ditemukan')

@section('content')
    <section class="mx-auto max-w-xl px-4 pt-24 text-center sm:px-6">
        <p class="font-display text-7xl text-ombak/40">404</p>
        <h1 class="mt-4 text-3xl sm:text-4xl">Halaman ini tidak ditemukan</h1>
        <p class="mt-4 text-palung/70">Alamatnya mungkin salah ketik, atau halamannya sudah dipindahkan.</p>
        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke beranda</a>
            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Lihat kamar</a>
        </div>
    </section>
@endsection
