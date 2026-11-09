<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Jalankan semua seeder:
 *   php artisan migrate:fresh --seed
 *
 * Urutan penting: users -> kategori -> jenis kamar -> artikel -> booking.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            RoomTypeSeeder::class,
            ArticleSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
