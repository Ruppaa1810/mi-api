<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@metalpoint.com'],
            [
                'name'     => 'Administrador',
                'password' => 'password',
                'role'     => 'admin',
            ]
        );
    }
}
