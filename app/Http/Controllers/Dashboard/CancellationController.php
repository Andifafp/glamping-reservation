<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Cancellation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Staff memproses pengajuan pembatalan. Penanggung jawab: Thoni.
 *
 * Setujui: cancellation 'approved', booking 'cancelled' (unit otomatis tersedia lagi
 *          karena booking cancelled tidak dihitung), catatan refund disimpan.
 * Tolak:   cancellation 'rejected', booking tetap berjalan, alasan staff disimpan.
 */
class CancellationController extends Controller
{
    public function index(Request $request): View
    {
        $statuses = [
            Cancellation::STATUS_PENDING  => 'Menunggu',
            Cancellation::STATUS_APPROVED => 'Disetujui',
            Cancellation::STATUS_REJECTED => 'Ditolak',
        ];
        $status = array_key_exists($request->query('status'), $statuses) ? $request->query('status') : Cancellation::STATUS_PENDING;

        $cancellations = Cancellation::with(['booking.roomType', 'user', 'processor'])
            ->where('status', $status)
            ->orderBy($status === Cancellation::STATUS_PENDING ? 'created_at' : 'processed_at', $status === Cancellation::STATUS_PENDING ? 'asc' : 'desc')
            ->paginate(15)
            ->withQueryString();

        $counts = Cancellation::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('dashboard.cancellations.index', compact('cancellations', 'statuses', 'status', 'counts'));
    }

    public function show(Cancellation $cancellation): View
    {
        $cancellation->load(['booking.roomType', 'booking.payments', 'user', 'processor']);

        return view('dashboard.cancellations.show', compact('cancellation'));
    }

    public function approve(Request $request, Cancellation $cancellation): RedirectResponse
    {
        $data = $request->validate([
            'refund_note' => ['nullable', 'string', 'max:1000'],
            'staff_note'  => ['nullable', 'string', 'max:1000'],
        ]);

        $done = DB::transaction(function () use ($cancellation, $data, $request) {
            $cancellation = Cancellation::whereKey($cancellation->id)->lockForUpdate()->firstOrFail();
            if ($cancellation->status !== Cancellation::STATUS_PENDING) {
                return false;
            }

            $booking = Booking::whereKey($cancellation->booking_id)->lockForUpdate()->firstOrFail();
            $amount = 'Rp' . number_format($cancellation->refund_amount, 0, ',', '.');

            $cancellation->update([
                'status'       => Cancellation::STATUS_APPROVED,
                'refund_note'  => ($data['refund_note'] ?? null)
                    ?: "Refund {$cancellation->refund_percentage}% ({$amount}) dicatat untuk diproses manual.",
                'staff_note'   => ($data['staff_note'] ?? null) ?: 'Pengajuan sesuai ketentuan.',
                'processed_by' => $request->user()->id,
                'processed_at' => now(),
            ]);

            $booking->update(['status' => Booking::STATUS_CANCELLED]);

            ActivityLog::record(
                'cancellation.approved',
                "Menyetujui pembatalan booking {$booking->booking_code}, refund {$cancellation->refund_percentage}% ({$amount})",
                $cancellation,
                ['refund_percentage' => $cancellation->refund_percentage, 'refund_amount' => $cancellation->refund_amount]
            );

            return true;
        });

        return redirect()->route('dashboard.cancellations.index')
            ->with($done ? 'status' : 'info', $done
                ? 'Pembatalan disetujui. Booking dibatalkan dan unit kamar tersedia lagi.'
                : 'Pengajuan ini sudah diproses sebelumnya.');
    }

    public function reject(Request $request, Cancellation $cancellation): RedirectResponse
    {
        $data = $request->validate([
            'staff_note' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'staff_note.required' => 'Tuliskan alasan penolakan untuk tamu.',
            'staff_note.min'      => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $done = DB::transaction(function () use ($cancellation, $data, $request) {
            $cancellation = Cancellation::with('booking')->whereKey($cancellation->id)->lockForUpdate()->firstOrFail();
            if ($cancellation->status !== Cancellation::STATUS_PENDING) {
                return false;
            }

            $cancellation->update([
                'status'            => Cancellation::STATUS_REJECTED,
                'staff_note'        => $data['staff_note'],
                'refund_percentage' => 0,
                'refund_amount'     => 0,
                'processed_by'      => $request->user()->id,
                'processed_at'      => now(),
            ]);

            ActivityLog::record(
                'cancellation.rejected',
                "Menolak pembatalan booking {$cancellation->booking->booking_code}",
                $cancellation,
                ['alasan' => $data['staff_note']]
            );

            return true;
        });

        return redirect()->route('dashboard.cancellations.index')
            ->with($done ? 'status' : 'info', $done ? 'Pengajuan pembatalan ditolak.' : 'Pengajuan ini sudah diproses sebelumnya.');
    }
}