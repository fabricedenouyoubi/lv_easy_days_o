<?php

namespace Modules\Roles\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'Voir Employe', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ajouter Employe', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Voir Detail Employe', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier Employe', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Changer Gestionnaire Employe', 'module' => 'RH', 'guard_name' => 'web']);


        Permission::firstOrCreate(['name' => 'Voir Entreprise', 'module' => 'ENTREPRISE', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ajouter Entreprise', 'module' => 'ENTREPRISE', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier Entreprise', 'module' => 'ENTREPRISE', 'guard_name' => 'web']);
    }
}
