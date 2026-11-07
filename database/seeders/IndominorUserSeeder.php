<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class IndominorUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'indominor'],
            [
                'rol' => 'Administrador',
                'nombres' => 'Indominor',
                'apellidos' => 'Administrador',
                'email' => null,
                'password' => 'admin1234',
                'estado' => true,
            ]
        );
    }
}
