<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@mail.com',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Kasir',
            'email'    => 'kasir@mail.com',
            'password' => 'password',
            'role'     => 'user',
        ]);

        User::create([
            'name'     => 'Gudang',
            'email'    => 'gudang@mail.com',
            'password' => 'password',
            'role'     => 'user',
        ]);
    }
}
