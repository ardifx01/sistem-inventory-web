<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'superadmin',
            'status' => 'active'
        ]);

        User::create([
            'name' => 'Admin Aktif',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        User::create([
            'name' => 'User Nonaktif',
            'email' => 'user@gmail.com',
            'password' => bcrypt('123456'),
            'role' => 'user',
            'status' => 'inactive'
        ]);
    }
}
