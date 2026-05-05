<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; 
use Illuminate\Support\Facades\Hash; 

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lama agar tidak duplikat (Opsional tapi bagus untuk testing)
        User::truncate();

        // Akun Owner (Daniel)
        User::create([
            'name' => 'Daniel Roger Kennedy',
            'email' => 'daniel@vesta.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);

        // Akun Admin untuk VESTA
        User::create([
            'name' => 'Staff Admin Vesta',
            'email' => 'admin@vesta.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
    }
}