<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo WAJIB (sesuai ketentuan tugas). Password otomatis di-hash
        // oleh cast 'hashed' di model User.
        $accounts = [
            ['name' => 'Admin Demo', 'email' => 'admin@demo.test', 'password' => 'Admin#2026', 'role' => User::ROLE_ADMIN, 'phone' => '081200000001'],
            ['name' => 'Staff Demo', 'email' => 'staff@demo.test', 'password' => 'Staff#2026', 'role' => User::ROLE_STAFF, 'phone' => '081200000002'],
            ['name' => 'User Demo',  'email' => 'user@demo.test',  'password' => 'User#2026',  'role' => User::ROLE_USER,  'phone' => '081200000003'],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account + ['is_active' => true, 'email_verified_at' => now()]
            );
        }

        // Staff tambahan supaya halaman log aktivitas punya lebih dari satu pelaku
        User::updateOrCreate(
            ['email' => 'rina.staff@demo.test'],
            ['name' => 'Rina Pratiwi', 'password' => 'Staff#2026', 'role' => User::ROLE_STAFF, 'phone' => '081200000004', 'is_active' => true]
        );

        // Beberapa user biasa untuk data booking dummy
        $guests = [
            ['Andi Saputra', 'andi@example.test', '081311110001'],
            ['Siti Rahmawati', 'siti@example.test', '081311110002'],
            ['Budi Santoso', 'budi@example.test', '081311110003'],
            ['Dewi Lestari', 'dewi@example.test', '081311110004'],
            ['Rizky Ramadhan', 'rizky@example.test', '081311110005'],
            ['Nadia Putri', 'nadia@example.test', '081311110006'],
        ];

        foreach ($guests as [$name, $email, $phone]) {
            User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'phone' => $phone, 'password' => 'Password#2026', 'role' => User::ROLE_USER, 'is_active' => true]
            );
        }
    }
}
