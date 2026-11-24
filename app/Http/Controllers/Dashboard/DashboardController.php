<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Cancellation;
use App\Models\Payment;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Beranda dashboard staff/admin. Penanggung jawab: Thoni.
 *
 * - Kartu: check-in hari ini, sedang menginap, pembatalan menunggu, pendapatan bulan ini (dummy)
 * - Grafik 1: reservasi dan pengajuan pembatalan per periode
 * - Grafik 2: tingkat pemakaian unit (okupansi) per jenis kamar pada periode yang sama
 * - Tabel transaksi terbaru
 *
 * Pengelompokan data dilakukan di PHP (bukan fungsi tanggal SQL) supaya
 * jalan di MySQL maupun SQLite.
 */
class DashboardController extends Controller
{
    private const PERIODS = [
        '7'   => '7 hari terakhir',
        '30'  => '30 hari terakhir',
        '180' => '6 bulan terakhir',
    ];

    public function __invoke(Request $request): View
    {
        $period = array_key_exists((string) $request->query('periode'), self::PERIODS) ? (string) $request->query('periode') : '30';
        $monthly = $period === '180';
        $today = today();
        $start = $monthly ? $today->copy()->startOfMonth()->subMonths(5) : $today->copy()->subDays((int) $period - 1);

        // ---------- Kartu ringkasan ----------
        $checkInsToday = Booking::with(['roomType', 'user'])
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereDate('check_in', $today)
            ->orderBy('contact_name')
            ->get();

        $stayingNow = Booking::where('status', Booking::STATUS_CONFIRMED)
            ->whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>', $today)
            ->sum('units');

        $pendingCancellations = Cancellation::where('status', Cancellation::STATUS_PENDING)->count();

        $monthStart = $today->copy()->startOfMonth();
        $revenueMonth = (int) Payment::where('status', Payment::STATUS_PAID)->where('paid_at', '>=', $monthStart)->sum('amount');
        $refundMonth = (int) Cancellation::where('status', Cancellation::STATUS_APPROVED)->where('processed_at', '>=', $monthStart)->sum('refund_amount');

        // ---------- Grafik 1: reservasi & pembatalan per periode ----------
        $buckets = $this->buckets($start, $today, $monthly);
        $keyOf = fn (Carbon $date) => $date->format($monthly ? 'Y-m' : 'Y-m-d');

        $bookingSeries = array_fill_keys(array_keys($buckets), 0);
        Booking::where('created_at', '>=', $start)->get(['created_at'])
            ->each(function ($b) use (&$bookingSeries, $keyOf) {
                $k = $keyOf($b->created_at);
                if (isset($bookingSeries[$k])) {
                    $bookingSeries[$k]++;
                }
            });

        $cancelSeries = array_fill_keys(array_keys($buckets), 0);
        Cancellation::where('created_at', '>=', $start)->get(['created_at'])
            ->each(function ($c) use (&$cancelSeries, $keyOf) {
                $k = $keyOf($c->created_at);
                if (isset($cancelSeries[$k])) {
                    $cancelSeries[$k]++;
                }
            });

        // ---------- Grafik 2: okupansi per jenis kamar ----------
        $windowEnd = $today->copy()->addDay(); // malam hari ini ikut dihitung
        $windowNights = (int) $start->diffInDays($windowEnd);

        $occupied = Booking::whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
            ->where('check_in', '<', $windowEnd->toDateString())
            ->where('check_out', '>', $start->toDateString())
            ->get(['room_type_id', 'check_in', 'check_out', 'units'])
            ->groupBy('room_type_id')
            ->map(fn ($rows) => $rows->sum(function ($b) use ($start, $windowEnd) {
                $from = $b->check_in->greaterThan($start) ? $b->check_in : $start;
                $to = $b->check_out->lessThan($windowEnd) ? $b->check_out : $windowEnd;

                return max(0, (int) $from->diffInDays($to)) * $b->units;
            }));

        $occupancy = RoomType::where('is_active', true)->orderBy('id')->get(['id', 'name', 'total_units'])
            ->map(fn ($room) => [
                'name'    => $room->name,
                'percent' => $room->total_units > 0 && $windowNights > 0
                    ? round(($occupied[$room->id] ?? 0) / ($room->total_units * $windowNights) * 100, 1)
                    : 0,
            ]);

        // ---------- Tabel transaksi ----------
        $transactions = Booking::with(['roomType', 'user'])->latest()->take(10)->get();

        return view('dashboard.index', [
            'periods'              => self::PERIODS,
            'period'               => $period,
            'checkInsToday'        => $checkInsToday,
            'stayingNow'           => (int) $stayingNow,
            'pendingCancellations' => $pendingCancellations,
            'revenueMonth'         => $revenueMonth,
            'refundMonth'          => $refundMonth,
            'chart'                => [
                'labels'        => array_values($buckets),
                'bookings'      => array_values($bookingSeries),
                'cancellations' => array_values($cancelSeries),
            ],
            'occupancy'            => $occupancy,
            'transactions'         => $transactions,
        ]);
    }

    /** @return array<string,string> kunci periode => label sumbu X */
    private function buckets(Carbon $start, Carbon $end, bool $monthly): array
    {
        $buckets = [];
        for ($d = $start->copy(); $d->lessThanOrEqualTo($end); $monthly ? $d->addMonth() : $d->addDay()) {
            $buckets[$d->format($monthly ? 'Y-m' : 'Y-m-d')] = $d->translatedFormat($monthly ? 'M Y' : 'd M');
        }

        return $buckets;
    }
}
