<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador', 'guard_name' => 'web'],
            ['name' => 'Vendedor', 'guard_name' => 'web'],
            ['name' => 'Comprador', 'guard_name' => 'web'],
            ['name' => 'Mantenedor', 'guard_name' => 'web'],
            ['name' => 'Analista', 'guard_name' => 'web'],
            ['name' => 'Almacen', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role){
            Role::firstOrCreate(['name' => $role['name']],$role);
        }

    }
}
