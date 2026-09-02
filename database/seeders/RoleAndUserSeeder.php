<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Data Role (Admin & Guru)
        $adminRole = Role::create([
            'name' => 'admin',
        ]);

        $guruRole = Role::create([
            'name' => 'guru',
        ]);

        // 2. Buat Akun Admin
        User::create([
            'role_id'  => $adminRole->id,
            'name'     => 'Administrator',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        // 3. Buat Akun Guru
        User::create([
            'role_id'  => $guruRole->id,
            'name'     => 'Guru Al-Huda',
            'email'    => 'guru@gmail.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
