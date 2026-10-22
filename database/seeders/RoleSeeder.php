<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        Role::firstOrCreate(['name' => 'Cliente']);

        // 2. Crear Usuario Administrador
        // El mail y la contraseña salen de las variables de entorno (en Render), así no quedan escritos en el repo
        $admin = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@metalpoint.com')],
            [
                'name' => 'Administrador',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password123')),
            ]
        );
        $admin->assignRole($adminRole);

        // Los clientes se crean solos con el registro (register les asigna el rol Cliente)
    }
}
