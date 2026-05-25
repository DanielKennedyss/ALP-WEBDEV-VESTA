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
        // --- INTERNAL STAFF ---

        // 1. Akun Owner (Daniel)
        User::create([
            'name' => 'Daniel Roger Kennedy',
            'email' => 'daniel@vesta.com',
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'status' => 'active',
        ]);

        // 2. Akun Manager (Nicho)
        User::create([
            'name' => 'Nicho',
            'email' => 'nicho@vesta.com',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'status' => 'active',
        ]);

        // 3. Akun Staff
        User::create([
            'name' => 'Jordan',
            'email' => 'jordan@vesta.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Rere',
            'email' => 'rere@vesta.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Staff Admin Vesta',
            'email' => 'admin@vesta.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        // --- CUSTOMERS (Dibutuhkan oleh TransactionSeeder) ---

        // 4. Akun Customer Spesifik (Untuk Testing Login Profil)
        User::create([
            'name' => 'Ella Customer',
            'email' => 'ella@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'status' => 'GOLD',
            'membership_level' => 'gold', // Menyesuaikan dengan migrasi VESTA
        ]);

        User::create([
            'name' => 'Angie Customer',
            'email' => 'angie@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'status' => 'SILVER',
            'membership_level' => 'silver',
        ]);

        // 5. Menambahkan 5 Customer Acak Tambahan
        $fakeCustomers = ['Qis', 'Budi Santoso', 'Siti Sarah', 'Jessica Wong', 'Ahmad'];
        foreach ($fakeCustomers as $name) {
            User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '', $name)) . '@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'status' => 'BRONZE',
                'membership_level' => 'bronze',
            ]);
        }
    }
}