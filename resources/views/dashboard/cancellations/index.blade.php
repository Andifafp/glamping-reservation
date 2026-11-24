{{--
    Daftar pengajuan pembatalan (staff) - Thoni
    Variabel: $cancellations (paginator), $statuses, $status, $counts
--}}
@extends('layouts.dashboard')

@section('title', 'Pembatalan')

@section('content')
    <nav class="flex flex-wrap gap-2" aria-label="Filter status">
        @foreach ($statuses as $value => $label)
            <a href="{{ route('dashboard.cancellations.index', ['status' => $value]) }}"
               class="flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-medium {{ $status === $value ? 'border-laut bg-laut text-white' : 'border-palung/15 text-palung/75 hover:text-laut' }}"
               @if ($status === $value) aria-current="page" @endif>
                {{ $label }}
                <span class="rounded-full px-2 text-xs {{ $status === $value ? 'bg-white/20' : 'bg-palung/10' }}">{{ $counts[$value] ?? 0 }}</span>
            </a>
        @endforeach
    </nav>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-palung/10 bg-white">
        <table class="w-full min-w-[48rem] text-left text-sm">
            <caption class="sr-only">Pengajuan pembatalan berstatus {{ $statuses[$status] }}</caption>
            <thead class="border-b border-palung/10 text-palung/60">
                <tr>
                    <th scope="col" class="px-5 py-3 font-medium">Booking</th>
                    <th scope="col" class="px-5 py-3 font-medium">Check-in</th>
                    <th scope="col" class="px-5 py-3 font-medium">Refund</th>
                    <th scope="col" class="px-5 py-3 font-medium">{{ $status === 'pending' ? 'Diajukan' : 'Diproses' }}</th>
                    <th scope="col" class="px-5 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-palung/10">
                @forelse ($cancellations as $cancellation)
                    @php $booking = $cancellation->booking; @endphp
                    <tr class="align-top">
                        <td class="px-5 py-4">
                            <p class="font-semibold">{{ $booking->contact_name }}</p>
                            <p class="text-palung/60">{{ $booking->roomType->name }}, <span class="font-mono text-xs">{{ $booking->booking_code }}</span></p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4">
                            {{ $booking->check_in->translatedFormat('d M Y') }}
                            <p class="text-palung/60">{{ $booking->with_insurance ? 'Asuransi' : 'Tanpa asuransi' }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4">
                            <p class="font-semibold">{{ $cancellation->refund_percentage }}%</p>
                            <p class="text-palung/60">Rp{{ number_format($cancellation->refund_amount, 0, ',', '.') }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-palung/70">
                            @if ($status === 'pending')
                                {{ $cancellation->created_at->translatedFormat('d M Y, H:i') }}
                            @else
                                {{ $cancellation->processed_at?->translatedFormat('d M Y, H:i') }}
                                <p>oleh {{ $cancellation->processor?->name ?? '-' }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('dashboard.cancellations.show', $cancellation) }}" class="btn {{ $status === 'pending' ? 'btn-primary' : 'btn-secondary' }} px-3 py-1.5">
                                {{ $status === 'pending' ? 'Proses' : 'Lihat' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-palung/60">
                            {{ $status === 'pending' ? 'Tidak ada pengajuan yang menunggu. Semua sudah diproses.' : 'Belum ada data.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $cancellations->links() }}</div>
@endsection
