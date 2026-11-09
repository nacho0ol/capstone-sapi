<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Admin Olivia',
                'email' => 'nacholivia13@gmail.com', // 👈 Masukkan email di sini (gunakan emailmu sendiri jika mau tes kirim link beneran)
                'password' => bcrypt('password123'),
                'is_deleted' => 0,
            ]
        );
    }
}