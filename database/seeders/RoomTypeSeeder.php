<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::pluck('id', 'slug');

        $rooms = [
            [
                'category' => 'safari-tent',
                'name' => 'Safari Tent Standard',
                'slug' => 'safari-tent-standard',
                'description' => 'Tenda safari untuk 2 orang dengan teras kecil menghadap hutan pinus.',
                'facilities' => ['Kasur queen', 'Kamar mandi dalam', 'Air panas', 'Sarapan 2 orang', 'Teras'],
                'capacity' => 2, 'total_units' => 6, 'price_per_night' => 650000,
            ],
            [
                'category' => 'safari-tent',
                'name' => 'Safari Tent Family',
                'slug' => 'safari-tent-family',
                'description' => 'Tenda safari luas untuk keluarga, dilengkapi dua kasur dan area duduk.',
                'facilities' => ['1 kasur queen + 2 kasur single', 'Kamar mandi dalam', 'Air panas', 'Sarapan 4 orang', 'Area api unggun'],
                'capacity' => 4, 'total_units' => 4, 'price_per_night' => 1050000,
            ],
            [
                'category' => 'kabin-kayu',
                'name' => 'Kabin Deluxe',
                'slug' => 'kabin-deluxe',
                'description' => 'Kabin kayu dengan jendela besar dan balkon pribadi.',
                'facilities' => ['Kasur king', 'Kamar mandi dalam', 'Pemanas ruangan', 'Sarapan 2 orang', 'Balkon'],
                'capacity' => 2, 'total_units' => 3, 'price_per_night' => 900000,
            ],
            [
                'category' => 'kabin-kayu',
                'name' => 'Kabin Keluarga',
                'slug' => 'kabin-keluarga',
                'description' => 'Kabin dua kamar untuk rombongan kecil, lengkap dengan dapur sederhana.',
                'facilities' => ['2 kamar tidur', 'Dapur kecil', 'Kamar mandi dalam', 'Sarapan 6 orang', 'Area BBQ'],
                'capacity' => 6, 'total_units' => 2, 'price_per_night' => 1600000,
            ],
            [
                'category' => 'dome',
                'name' => 'Stargazing Dome',
                'slug' => 'stargazing-dome',
                'description' => 'Tenda kubah dengan atap transparan untuk menikmati bintang di malam hari.',
                'facilities' => ['Kasur queen', 'Atap transparan', 'Kamar mandi dalam', 'Sarapan 2 orang', 'Teleskop kecil'],
                'capacity' => 2, 'total_units' => 3, 'price_per_night' => 1200000,
            ],
        ];

        foreach ($rooms as $room) {
            $categorySlug = $room['category'];
            unset($room['category']);

            RoomType::updateOrCreate(
                ['slug' => $room['slug']],
                $room + ['category_id' => $cat[$categorySlug], 'is_active' => true]
            );
        }
    }
}
