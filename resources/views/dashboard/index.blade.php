{{--
    Beranda dashboard staff/admin.
    SEMENTARA dari Marcel. Thoni mengganti isi halaman ini dengan:
    kartu ringkasan (booking hari ini, pembatalan menunggu, pendapatan dummy),
    tabel transaksi, dan grafik.
--}}
@extends('layouts.dashboard')

@section('title', 'Beranda')

@section('content')
    <div class="panel">
        <p class="text-lg">Halo, <strong class="font-semibold">{{ auth()->user()->name }}</strong>.</p>
        <p class="mt-2 text-palung/70">
            Kamu masuk sebagai {{ auth()->user()->isAdmin() ? 'admin' : 'staff' }}.
            Ringkasan reservasi, pembatalan, pendapatan, dan grafik akan tampil di sini.
        </p>
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        @foreach (['Booking hari ini', 'Pembatalan menunggu', 'Pendapatan bulan ini'] as $label)
            <div class="rounded-2xl border border-dashed border-palung/20 p-5">
                <p class="text-sm text-palung/60">{{ $label }}</p>
                <p class="mt-2 font-display text-3xl text-palung/30">-</p>
            </div>
        @endforeach
    </div>
@endsection
