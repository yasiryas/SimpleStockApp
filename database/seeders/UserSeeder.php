<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        User::create([
            'name'              => 'Admin',
            'email'             => 'admin@mail.com',
            'password'          => 'password',
            'role'              => 'admin',
            'email_verified_at' => $now,
        ]);

        User::create([
            'name'              => 'Kasir',
            'email'             => 'kasir@mail.com',
            'password'          => 'password',
            'role'              => 'user',
            'email_verified_at' => $now,
        ]);

        User::create([
            'name'              => 'Gudang',
            'email'             => 'gudang@mail.com',
            'password'          => 'password',
            'role'              => 'user',
            'email_verified_at' => $now,
        ]);
    }
}
