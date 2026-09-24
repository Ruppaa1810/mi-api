<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@metalpoint.com');

        User::where('role', 'admin')->where('email', '!=', $email)->get()->each(function (User $usuario) {
            $usuario->tokens()->delete();
            $usuario->delete();
        });

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => 'Administrador',
                'password' => env('ADMIN_PASSWORD', 'password'),
                'role'     => 'admin',
            ]
        );
    }
}
