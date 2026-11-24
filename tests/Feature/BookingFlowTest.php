<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\RoomType;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\RoomTypeSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tes alur reservasi. Jalankan: php artisan test --filter=BookingFlowTest
 * Memakai database tes (bukan glamping_db), jadi data asli aman.
 */
class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([UserSeeder::class, CategorySeeder::class, RoomTypeSeeder::class]);
    }

    private function payload(RoomType $room, array $override = []): array
    {
        return array_merge([
            'room_type_id'   => $room->id,
            'check_in'       => now()->addDays(10)->toDateString(),
            'check_out'      => now()->addDays(12)->toDateString(),
            'units'          => 1,
            'guests'         => 2,
            'contact_name'   => 'User Demo',
            'contact_email'  => 'user@demo.test',
            'contact_phone'  => '081200000003',
            'with_insurance' => 1,
        ], $override);
    }

    public function test_tamu_harus_login_untuk_reservasi(): void
    {
        $this->get('/reservasi')->assertRedirect('/masuk');
    }

    public function test_reservasi_tersimpan_dengan_total_dan_asuransi_benar(): void
    {
        $user = User::where('email', 'user@demo.test')->first();
        $room = RoomType::where('slug', 'kabin-deluxe')->first(); // Rp900.000/malam

        $this->actingAs($user)->post('/reservasi', $this->payload($room))->assertRedirect();

        $booking = Booking::first();
        $this->assertSame('pending', $booking->status);
        $this->assertSame(2, $booking->nights);
        $this->assertEquals(1800000, $booking->subtotal);
        $this->assertEquals(180000, $booking->insurance_fee);
        $this->assertEquals(1980000, $booking->total_price);
    }

    public function test_reservasi_ditolak_jika_stok_tidak_cukup(): void
    {
        $user = User::where('email', 'user@demo.test')->first();
        $room = RoomType::where('slug', 'kabin-keluarga')->first(); // 2 unit

        $this->actingAs($user)->post('/reservasi', $this->payload($room, ['units' => 2, 'guests' => 4]))->assertRedirect();
        $this->actingAs($user)
            ->post('/reservasi', $this->payload($room, ['units' => 1, 'guests' => 2]))
            ->assertSessionHasErrors('units');

        $this->assertSame(1, Booking::count());
    }

    public function test_tanggal_checkout_sama_dengan_checkin_lain_tidak_bentrok(): void
    {
        $user = User::where('email', 'user@demo.test')->first();
        $room = RoomType::where('slug', 'kabin-keluarga')->first();

        $this->actingAs($user)->post('/reservasi', $this->payload($room, ['units' => 2, 'guests' => 4]));
        $this->actingAs($user)->post('/reservasi', $this->payload($room, [
            'check_in'  => now()->addDays(12)->toDateString(),
            'check_out' => now()->addDays(13)->toDateString(),
            'units' => 2, 'guests' => 4,
        ]))->assertSessionHasNoErrors();

        $this->assertSame(2, Booking::count());
    }

    public function test_bayar_mengubah_status_menjadi_confirmed(): void
    {
        $user = User::where('email', 'user@demo.test')->first();
        $room = RoomType::first();
        $this->actingAs($user)->post('/reservasi', $this->payload($room));
        $booking = Booking::first();

        $this->actingAs($user)
            ->post("/reservasi/{$booking->booking_code}/bayar", ['method' => 'qris'])
            ->assertRedirect("/reservasi/{$booking->booking_code}/sukses");

        $this->assertSame('confirmed', $booking->fresh()->status);
        $this->assertSame(1, $booking->payments()->count());
    }

    public function test_user_lain_tidak_bisa_membuka_booking_orang_lain(): void
    {
        $owner = User::where('email', 'user@demo.test')->first();
        $other = User::where('email', 'andi@example.test')->first();
        $this->actingAs($owner)->post('/reservasi', $this->payload(RoomType::first()));
        $booking = Booking::first();

        $this->actingAs($other)->get("/reservasi/{$booking->booking_code}/bayar")->assertForbidden();
    }

    public function test_cek_booking_harus_cocok_kode_dan_kontak(): void
    {
        $user = User::where('email', 'user@demo.test')->first();
        $this->actingAs($user)->post('/reservasi', $this->payload(RoomType::first()));
        $code = Booking::first()->booking_code;
        auth()->logout();

        $this->post('/cek-booking', ['booking_code' => $code, 'contact' => 'salah@email.test'])
            ->assertSessionHasErrors('booking_code');

        $this->post('/cek-booking', ['booking_code' => $code, 'contact' => '+6281200000003'])
            ->assertOk()
            ->assertSee($code);
    }
}
