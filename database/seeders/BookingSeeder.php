<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Cancellation;
use App\Models\Payment;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Data transaksi dummy agar dashboard, grafik, dan profil user tidak kosong.
 * Tanggal dibuat relatif terhadap hari ini, jadi data selalu "terbaru"
 * setiap kali seeder dijalankan ulang.
 *
 * Seeder ini menghormati stok: booking tidak dibuat jika unit tidak cukup.
 */
class BookingSeeder extends Seeder
{
    private int $paymentCounter = 0;

    public function run(): void
    {
        mt_srand(2026); // hasil acak selalu sama setiap dijalankan

        $today  = Carbon::today();
        $rooms  = RoomType::all();
        $guests = User::where('role', User::ROLE_USER)->where('email', '!=', 'user@demo.test')->get();
        $staff  = User::whereIn('email', ['staff@demo.test', 'rina.staff@demo.test'])->get();

        // ---------- 1. Booking terkontrol milik user@demo.test ----------
        $demo = User::where('email', 'user@demo.test')->first();

        // Sudah selesai menginap
        $this->makeBooking($demo, $rooms[0], $today->copy()->subDays(40), 2, 1, false, Booking::STATUS_COMPLETED);

        // Akan datang, dengan asuransi -> bisa dicoba ajukan pembatalan (refund 100%)
        $this->makeBooking($demo, $rooms[4], $today->copy()->addDays(14), 2, 1, true, Booking::STATUS_CONFIRMED);

        // Akan datang, pembatalan masih menunggu staff
        $pendingCancel = $this->makeBooking($demo, $rooms[2], $today->copy()->addDays(9), 1, 1, false, Booking::STATUS_CONFIRMED);
        $this->makeCancellation($pendingCancel, $today->copy()->subDay(), Cancellation::STATUS_PENDING, null, 'Ada acara keluarga mendadak.');

        // Sudah dibatalkan dan disetujui
        $approved = $this->makeBooking($demo, $rooms[1], $today->copy()->addDays(20), 2, 1, true, Booking::STATUS_CANCELLED);
        $this->makeCancellation($approved, $today->copy()->subDays(3), Cancellation::STATUS_APPROVED, $staff[0], 'Jadwal cuti kantor berubah.');

        // ---------- 2. Booking hari ini (untuk kartu "booking hari ini") ----------
        foreach ([[0, 2], [2, 1], [3, 1]] as [$roomIndex, $units]) {
            $this->makeBooking($guests->random(), $rooms[$roomIndex], $today->copy(), mt_rand(1, 3), $units, (bool) mt_rand(0, 1), Booking::STATUS_CONFIRMED);
        }

        // ---------- 3. Booking acak: 90 hari ke belakang s.d. 30 hari ke depan ----------
        for ($i = 0; $i < 60; $i++) {
            $checkIn = $today->copy()->addDays(mt_rand(-90, 30));
            $nights  = mt_rand(1, 3);
            $room    = $rooms[mt_rand(0, $rooms->count() - 1)];
            $units   = mt_rand(1, 2);
            $insured = mt_rand(1, 100) <= 35;

            $checkOut = $checkIn->copy()->addDays($nights);
            if ($checkOut->lte($today)) {
                $status = Booking::STATUS_COMPLETED;
            } elseif ($checkIn->gt($today) && mt_rand(1, 100) <= 15) {
                $status = Booking::STATUS_PENDING; // belum dibayar
            } else {
                $status = Booking::STATUS_CONFIRMED;
            }

            // Sekitar 15% booking dibatalkan
            $cancelRoll = mt_rand(1, 100);
            if ($status !== Booking::STATUS_PENDING && $cancelRoll <= 15) {
                $requestedAt = $checkIn->copy()->subDays(mt_rand(1, 10));
                if ($requestedAt->gt($today)) {
                    $requestedAt = $today->copy();
                }

                if ($checkIn->gt($today) && $cancelRoll <= 4) {
                    // Masih menunggu keputusan staff
                    $booking = $this->makeBooking($guests->random(), $room, $checkIn, $nights, $units, $insured, Booking::STATUS_CONFIRMED);
                    $this->makeCancellation($booking, $requestedAt, Cancellation::STATUS_PENDING, null);
                } elseif ($cancelRoll <= 7) {
                    // Ditolak: booking tetap berjalan
                    $booking = $this->makeBooking($guests->random(), $room, $checkIn, $nights, $units, $insured, $status);
                    $this->makeCancellation($booking, $requestedAt, Cancellation::STATUS_REJECTED, $staff->random());
                } else {
                    // Disetujui: booking menjadi cancelled
                    $booking = $this->makeBooking($guests->random(), $room, $checkIn, $nights, $units, $insured, Booking::STATUS_CANCELLED);
                    $this->makeCancellation($booking, $requestedAt, Cancellation::STATUS_APPROVED, $staff->random());
                }
                continue;
            }

            $this->makeBooking($guests->random(), $room, $checkIn, $nights, $units, $insured, $status);
        }
    }

    /**
     * Membuat satu booking + pembayarannya. Mengembalikan null jika unit tidak cukup.
     */
    private function makeBooking(User $user, RoomType $room, Carbon $checkIn, int $nights, int $units, bool $insured, string $status): ?Booking
    {
        $checkOut = $checkIn->copy()->addDays($nights);

        // Booking yang nantinya cancelled tidak menahan unit, jadi tidak perlu dicek
        if ($status !== Booking::STATUS_CANCELLED
            && $room->availableUnits($checkIn->toDateString(), $checkOut->toDateString()) < $units) {
            return null;
        }

        $subtotal     = $room->price_per_night * $nights * $units;
        $insuranceFee = $insured ? (int) round($subtotal * config('glamping.insurance_rate') / 100) : 0;
        $createdAt    = $checkIn->copy()->subDays(mt_rand(3, 25))->setTime(mt_rand(8, 21), mt_rand(0, 59));
        if ($createdAt->gt(now())) {
            $createdAt = now()->subHours(mt_rand(1, 48));
        }

        $booking = Booking::create([
            'booking_code'    => Booking::generateCode(),
            'user_id'         => $user->id,
            'room_type_id'    => $room->id,
            'contact_name'    => $user->name,
            'contact_email'   => $user->email,
            'contact_phone'   => $user->phone ?? '081200000000',
            'check_in'        => $checkIn->toDateString(),
            'check_out'       => $checkOut->toDateString(),
            'nights'          => $nights,
            'units'           => $units,
            'guests'          => min($room->capacity * $units, mt_rand(1, $room->capacity * $units)),
            'price_per_night' => $room->price_per_night,
            'subtotal'        => $subtotal,
            'with_insurance'  => $insured,
            'insurance_fee'   => $insuranceFee,
            'total_price'     => $subtotal + $insuranceFee,
            'status'          => $status,
            'confirmed_at'    => $status === Booking::STATUS_PENDING ? null : $createdAt->copy()->addMinutes(mt_rand(5, 120)),
        ]);
        $booking->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->save();

        $this->paymentCounter++;
        Payment::create([
            'booking_id' => $booking->id,
            'reference'  => 'PAY-' . $createdAt->format('Ymd') . '-' . str_pad((string) $this->paymentCounter, 4, '0', STR_PAD_LEFT),
            'method'     => array_rand(config('glamping.payment_methods')),
            'amount'     => $booking->total_price,
            'status'     => $status === Booking::STATUS_PENDING ? Payment::STATUS_PENDING : Payment::STATUS_PAID,
            'paid_at'    => $booking->confirmed_at,
        ]);

        return $booking;
    }

    private function makeCancellation(?Booking $booking, Carbon $requestedAt, string $status, ?User $staff, ?string $reason = null): void
    {
        if (! $booking) {
            return; // booking batal dibuat karena unit penuh
        }

        $percentage = $booking->refundPercentageAt($requestedAt);
        $amount     = (int) round($booking->total_price * $percentage / 100);
        $processed  = $status !== Cancellation::STATUS_PENDING;
        $processedAt = $processed ? $requestedAt->copy()->addHours(mt_rand(2, 30)) : null;
        if ($processedAt && $processedAt->gt(now())) {
            $processedAt = now()->subMinutes(mt_rand(10, 120)); // jangan sampai di masa depan
        }

        $cancellation = Cancellation::create([
            'booking_id'        => $booking->id,
            'user_id'           => $booking->user_id,
            'reason'            => $reason ?? collect([
                'Ada keperluan mendadak.',
                'Kondisi kesehatan tidak memungkinkan.',
                'Jadwal pekerjaan berubah.',
                'Perjalanan ditunda karena cuaca.',
            ])->random(),
            'status'            => $status,
            'refund_percentage' => $percentage,
            'refund_amount'     => $amount,
            'refund_note'       => $status === Cancellation::STATUS_APPROVED
                ? "Refund {$percentage}% (Rp" . number_format($amount, 0, ',', '.') . ') dicatat untuk diproses manual.'
                : null,
            'staff_note'        => match ($status) {
                Cancellation::STATUS_APPROVED => 'Pengajuan sesuai ketentuan.',
                Cancellation::STATUS_REJECTED => 'Tamu sudah check-in / pengajuan di luar ketentuan.',
                default => null,
            },
            'processed_by'      => $processed ? $staff?->id : null,
            'processed_at'      => $processedAt,
        ]);
        $cancellation->forceFill(['created_at' => $requestedAt, 'updated_at' => $processedAt ?? $requestedAt])->save();

        if ($processed && $staff) {
            ActivityLog::create([
                'user_id'      => $staff->id,
                'action'       => 'cancellation.' . $status,
                'description'  => ($status === Cancellation::STATUS_APPROVED ? 'Menyetujui' : 'Menolak')
                                  . " pembatalan booking {$booking->booking_code}",
                'subject_type' => $cancellation->getMorphClass(),
                'subject_id'   => $cancellation->id,
                'properties'   => ['refund_percentage' => $percentage, 'refund_amount' => $amount],
                'ip_address'   => '127.0.0.1',
                'created_at'   => $processedAt,
            ]);
        }
    }
}
