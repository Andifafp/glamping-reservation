{{--
    Profil user: data diri + riwayat reservasi + status pembatalan - Thoni
    Variabel:
        $user     : user yang login
        $bookings : Booking milik $user, dengan relasi roomType dan latestCancellation,
                    urut check_in terbaru
--}}
@extends('layouts.app')

@section('title', 'Profil saya')

@section('content')
    <section class="mx-auto max-w-5xl px-4 pt-14 sm:px-6">
        <div class="flex flex-wrap items-start justify-between gap-6">
            <div class="flex items-center gap-4">
                <span class="flex size-14 items-center justify-center rounded-full bg-laut font-display text-2xl text-white" aria-hidden="true">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
                <div>
                    <h1 class="text-3xl sm:text-4xl">{{ $user->name }}</h1>
                    <p class="mt-1 text-sm text-palung/65">{{ $user->email }}@if ($user->phone), {{ $user->phone }}@endif</p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="btn btn-secondary">Ubah data diri</a>
        </div>

        <div class="mt-12 flex flex-wrap items-end justify-between gap-4">
            <h2 class="text-2xl sm:text-3xl">Riwayat reservasi</h2>
            <a href="{{ route('booking.create') }}" class="text-sm font-semibold text-laut hover:underline">Pesan kamar lagi</a>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($bookings as $booking)
                @php $cancellation = $booking->latestCancellation; @endphp
                <article class="rounded-2xl border border-palung/10 bg-white p-5 sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                @include('partials.status-badge', ['status' => $booking->status])
                                @if ($cancellation)
                                    @include('partials.status-badge', ['status' => $cancellation->status, 'type' => 'cancellation'])
                                @endif
                            </div>
                            <h3 class="mt-3 text-xl">{{ $booking->roomType->name }}</h3>
                            <p class="mt-1 text-sm text-palung/65">
                                {{ $booking->check_in->translatedFormat('d M Y') }} sampai {{ $booking->check_out->translatedFormat('d M Y') }},
                                {{ $booking->units }} unit
                            </p>
                        </div>
                        <div class="sm:text-right">
                            <p class="font-display text-xl">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</p>
                            <p class="mt-1 text-sm text-palung/55">{{ $booking->booking_code }}</p>
                        </div>
                    </div>

                    @if ($cancellation)
                        <div class="mt-4 rounded-xl bg-garam px-4 py-3 text-sm leading-6 text-palung/80">
                            @switch($cancellation->status)
                                @case('pending')
                                    Pengajuan pembatalan sedang diperiksa staf. Perkiraan refund
                                    {{ $cancellation->refund_percentage }}% (Rp{{ number_format($cancellation->refund_amount, 0, ',', '.') }}).
                                    @break
                                @case('approved')
                                    Pembatalan disetujui. Refund {{ $cancellation->refund_percentage }}%
                                    (Rp{{ number_format($cancellation->refund_amount, 0, ',', '.') }}) dicatat untuk diproses.
                                    @break
                                @case('rejected')
                                    Pembatalan ditolak.
                                    @if ($cancellation->staff_note) Alasan: {{ $cancellation->staff_note }} @endif
                                    @break
                            @endswitch
                        </div>
                    @endif

                    <div class="mt-5 flex flex-wrap gap-2">
                        @if ($booking->status === 'pending')
                            <a href="{{ route('booking.payment', $booking) }}" class="btn btn-primary px-4 py-2">Lanjutkan pembayaran</a>
                        @endif
                        @if ($booking->status === 'confirmed')
                            <a href="{{ route('booking.check', ['kode' => $booking->booking_code]) }}" class="btn btn-secondary px-4 py-2">Kartu check-in</a>
                        @endif
                        @if ($booking->canBeCancelled())
                            <a href="{{ route('cancellations.create', $booking) }}" class="btn px-4 py-2 text-red-700 hover:bg-red-50">Ajukan pembatalan</a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-palung/20 p-10 text-center">
                    <p class="font-semibold">Belum ada reservasi.</p>
                    <p class="mt-1 text-sm text-palung/65">Pilih kamar dan tanggal, riwayatnya akan muncul di sini.</p>
                    <a href="{{ route('rooms.index') }}" class="btn btn-primary mt-5">Lihat kamar</a>
                </div>
            @endforelse
        </div>
    </section>
@endsection
