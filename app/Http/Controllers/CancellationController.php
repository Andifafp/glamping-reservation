<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Cancellation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Pengajuan pembatalan oleh user. Penanggung jawab: Thoni.
 * Booking baru berubah menjadi 'cancelled' setelah staff menyetujui
 * (lihat Dashboard\CancellationController).
 */
class CancellationController extends Controller
{
    public function create(Request $request, Booking $booking): View|RedirectResponse
    {
        $this->authorizeOwner($request, $booking);
        $booking->load(['roomType', 'latestCancellation']);

        if (! $booking->canBeCancelled()) {
            return redirect()->route('profile.index')->with('info', $this->reasonCannotCancel($booking));
        }

        $percentage = $booking->refundPercentageAt(now());

        return view('cancellations.create', [
            'booking'          => $booking,
            'refundPercentage' => $percentage,
            'refundAmount'     => (int) round($booking->total_price * $percentage / 100),
        ]);
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeOwner($request, $booking);

        $data = $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'reason.required' => 'Tuliskan alasan pembatalan.',
            'reason.min'      => 'Alasan minimal 10 karakter.',
        ]);

        $created = DB::transaction(function () use ($booking, $data, $request) {
            // Kunci booking supaya klik dua kali tidak membuat dua pengajuan
            $booking = Booking::with('latestCancellation')->whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if (! $booking->canBeCancelled()) {
                return null;
            }

            $percentage = $booking->refundPercentageAt(now());

            return Cancellation::create([
                'booking_id'        => $booking->id,
                'user_id'           => $request->user()->id,
                'reason'            => $data['reason'],
                'status'            => Cancellation::STATUS_PENDING,
                'refund_percentage' => $percentage,
                'refund_amount'     => (int) round($booking->total_price * $percentage / 100),
            ]);
        });

        if (! $created) {
            return redirect()->route('profile.index')->with('info', $this->reasonCannotCancel($booking->fresh(['latestCancellation'])));
        }

        return redirect()->route('profile.index')
            ->with('status', "Pengajuan pembatalan {$booking->booking_code} terkirim. Staf akan memeriksanya.");
    }

    private function reasonCannotCancel(Booking $booking): string
    {
        return match (true) {
            $booking->latestCancellation?->status === Cancellation::STATUS_PENDING => 'Pengajuan pembatalan untuk booking ini masih diproses.',
            $booking->status === Booking::STATUS_PENDING   => 'Booking ini belum dibayar. Booking yang belum dibayar tidak perlu dibatalkan.',
            $booking->status === Booking::STATUS_CANCELLED => 'Booking ini sudah dibatalkan.',
            default => 'Booking ini tidak bisa dibatalkan karena tanggal check-in sudah lewat.',
        };
    }

    private function authorizeOwner(Request $request, Booking $booking): void
    {
        abort_unless((int) $booking->user_id === (int) $request->user()->id, 403);
    }
}
