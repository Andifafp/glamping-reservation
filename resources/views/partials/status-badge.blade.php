{{--
    Label status booking atau pembatalan.
    Parameter: $status, $type ('booking' atau 'cancellation', default 'booking')
--}}
@php
    $styles = [
        'booking' => [
            'pending'   => ['Menunggu pembayaran', 'bg-fajar/20 text-fajar-tua'],
            'confirmed' => ['Terkonfirmasi', 'bg-buih text-laut-tua'],
            'cancelled' => ['Dibatalkan', 'bg-red-50 text-red-800'],
            'completed' => ['Selesai', 'bg-palung/10 text-palung'],
        ],
        'cancellation' => [
            'pending'  => ['Pembatalan diproses', 'bg-fajar/20 text-fajar-tua'],
            'approved' => ['Pembatalan disetujui', 'bg-buih text-laut-tua'],
            'rejected' => ['Pembatalan ditolak', 'bg-red-50 text-red-800'],
        ],
    ];
    [$label, $color] = $styles[$type ?? 'booking'][$status] ?? [ucfirst($status), 'bg-palung/10 text-palung'];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $color }}">{{ $label }}</span>
