{{--
    Beranda dashboard staff/admin - Thoni
    Variabel dari Dashboard\DashboardController:
        $periods, $period, $checkInsToday, $stayingNow, $pendingCancellations,
        $revenueMonth, $refundMonth, $chart (labels, bookings, cancellations),
        $occupancy (name, percent), $transactions
    Grafik memakai Chart.js (CDN).
--}}
@extends('layouts.dashboard')

@section('title', 'Beranda')

@section('content')
    <p class="text-palung/70">Halo, <strong class="font-semibold text-palung">{{ auth()->user()->name }}</strong>. Ini ringkasan hari {{ now()->translatedFormat('l, d F Y') }}.</p>

    {{-- Kartu ringkasan --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-palung/10 bg-white p-5">
            <p class="text-sm text-palung/60">Check-in hari ini</p>
            <p class="mt-2 font-display text-4xl">{{ $checkInsToday->count() }}</p>
            <p class="mt-1 text-sm text-palung/60">{{ $checkInsToday->sum('units') }} unit</p>
        </div>
        <div class="rounded-2xl border border-palung/10 bg-white p-5">
            <p class="text-sm text-palung/60">Unit sedang terisi</p>
            <p class="mt-2 font-display text-4xl">{{ $stayingNow }}</p>
            <p class="mt-1 text-sm text-palung/60">tamu yang sedang menginap</p>
        </div>
        <a href="{{ route('dashboard.cancellations.index') }}" class="rounded-2xl border p-5 {{ $pendingCancellations ? 'border-fajar/60 bg-fajar/10' : 'border-palung/10 bg-white' }}">
            <p class="text-sm text-palung/60">Pembatalan menunggu</p>
            <p class="mt-2 font-display text-4xl {{ $pendingCancellations ? 'text-fajar-tua' : '' }}">{{ $pendingCancellations }}</p>
            <p class="mt-1 text-sm font-semibold text-laut">Proses sekarang</p>
        </a>
        <div class="rounded-2xl bg-palung p-5 text-white">
            <p class="text-sm text-white/70">Pendapatan bulan ini (dummy)</p>
            <p class="mt-2 font-display text-3xl">Rp{{ number_format($revenueMonth, 0, ',', '.') }}</p>
            <p class="mt-1 text-sm text-white/60">Refund dicatat Rp{{ number_format($refundMonth, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Grafik --}}
    <section class="mt-8 rounded-2xl border border-palung/10 bg-white p-5 sm:p-6" aria-labelledby="judul-grafik">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 id="judul-grafik" class="text-2xl">Reservasi dan pembatalan</h2>
            <nav class="flex flex-wrap gap-2" aria-label="Pilih periode">
                @foreach ($periods as $value => $label)
                    <a href="{{ route('dashboard.index', ['periode' => $value]) }}"
                       class="rounded-full border px-3 py-1.5 text-sm font-medium {{ $period === (string) $value ? 'border-laut bg-laut text-white' : 'border-palung/15 text-palung/75 hover:text-laut' }}"
                       @if ($period === (string) $value) aria-current="true" @endif>{{ $label }}</a>
                @endforeach
            </nav>
        </div>
        <div class="relative mt-6 h-72">
            <canvas id="grafik-reservasi" role="img" aria-label="Grafik jumlah reservasi dan pengajuan pembatalan, {{ $periods[$period] }}"></canvas>
        </div>
    </section>

    <section class="mt-6 rounded-2xl border border-palung/10 bg-white p-5 sm:p-6" aria-labelledby="judul-okupansi">
        <h2 id="judul-okupansi" class="text-2xl">Tingkat pemakaian unit</h2>
        <p class="mt-1 text-sm text-palung/60">Persentase malam-unit yang terisi per jenis kamar, {{ $periods[$period] }}.</p>
        <div class="relative mt-6" style="height: {{ max(180, count($occupancy) * 48) }}px">
            <canvas id="grafik-okupansi" role="img" aria-label="Grafik okupansi per jenis kamar"></canvas>
        </div>
    </section>

    <div class="mt-6 space-y-6">
        {{-- Check-in hari ini --}}
        <section class="min-w-0 rounded-2xl border border-palung/10 bg-white p-5 sm:p-6" aria-labelledby="judul-checkin">
            <h2 id="judul-checkin" class="text-2xl">Check-in hari ini</h2>
            <ul class="mt-4 divide-y divide-palung/10">
                @forelse ($checkInsToday as $booking)
                    <li class="flex items-start justify-between gap-3 py-3 text-sm">
                        <div>
                            <p class="font-semibold">{{ $booking->contact_name }}</p>
                            <p class="text-palung/60">{{ $booking->roomType->name }}, {{ $booking->units }} unit, {{ $booking->nights }} malam</p>
                        </div>
                        <span class="font-mono text-xs text-palung/60">{{ $booking->booking_code }}</span>
                    </li>
                @empty
                    <li class="py-6 text-sm text-palung/60">Tidak ada tamu yang check-in hari ini.</li>
                @endforelse
            </ul>
        </section>

        {{-- Transaksi terbaru --}}
        <section class="min-w-0 rounded-2xl border border-palung/10 bg-white" aria-labelledby="judul-transaksi">
            <h2 id="judul-transaksi" class="p-5 pb-0 text-2xl sm:p-6 sm:pb-0">Transaksi terbaru</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[40rem] text-left text-sm">
                    <caption class="sr-only">10 reservasi terbaru</caption>
                    <thead class="border-y border-palung/10 text-palung/60">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-medium">Kode</th>
                            <th scope="col" class="px-5 py-3 font-medium">Tamu / kamar</th>
                            <th scope="col" class="px-5 py-3 font-medium">Tanggal</th>
                            <th scope="col" class="px-5 py-3 font-medium">Status</th>
                            <th scope="col" class="px-5 py-3 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-palung/10">
                        @forelse ($transactions as $booking)
                            <tr>
                                <td class="px-5 py-3 font-mono text-xs">{{ $booking->booking_code }}</td>
                                <td class="px-5 py-3">
                                    <p class="font-medium">{{ $booking->contact_name }}</p>
                                    <p class="text-palung/60">{{ $booking->roomType->name }}</p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-3 text-palung/70">{{ $booking->check_in->translatedFormat('d M') }} - {{ $booking->check_out->translatedFormat('d M Y') }}</td>
                                <td class="px-5 py-3">@include('partials.status-badge', ['status' => $booking->status])</td>
                                <td class="whitespace-nowrap px-5 py-3 text-right font-semibold">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-palung/60">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (() => {
        if (typeof Chart === 'undefined') return;

        const chart = @json($chart);
        const occupancy = @json($occupancy);
        Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
        Chart.defaults.color = '#4c6472';

        new Chart(document.getElementById('grafik-reservasi'), {
            data: {
                labels: chart.labels,
                datasets: [
                    { type: 'bar', label: 'Reservasi', data: chart.bookings, backgroundColor: '#145C7A', borderRadius: 4, maxBarThickness: 32 },
                    { type: 'line', label: 'Pengajuan pembatalan', data: chart.cancellations, borderColor: '#F2B33D', backgroundColor: '#F2B33D', tension: 0.3, pointRadius: 3 },
                ],
            },
            options: {
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(11,42,60,0.06)' } },
                    x: { grid: { display: false } },
                },
                plugins: { legend: { position: 'bottom' } },
            },
        });

        new Chart(document.getElementById('grafik-okupansi'), {
            type: 'bar',
            data: {
                labels: occupancy.map(r => r.name),
                datasets: [{ label: 'Okupansi (%)', data: occupancy.map(r => r.percent), backgroundColor: '#2A8FA6', borderRadius: 4, maxBarThickness: 24 }],
            },
            options: {
                indexAxis: 'y',
                maintainAspectRatio: false,
                scales: { x: { min: 0, max: 100, ticks: { callback: v => v + '%' } }, y: { grid: { display: false } } },
                plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => c.parsed.x + '% terisi' } } },
            },
        });
    })();
</script>
@endpush
