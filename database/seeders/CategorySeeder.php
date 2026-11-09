<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Safari Tent', 'slug' => 'safari-tent', 'description' => 'Tenda kanvas besar dengan rangka kayu, nuansa petualangan dengan kenyamanan kamar hotel.'],
            ['name' => 'Kabin Kayu',  'slug' => 'kabin-kayu',  'description' => 'Kabin kayu tertutup, cocok untuk keluarga dan cuaca dingin pegunungan.'],
            ['name' => 'Dome',        'slug' => 'dome',        'description' => 'Tenda kubah transparan sebagian, bisa melihat langit malam dari tempat tidur.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
