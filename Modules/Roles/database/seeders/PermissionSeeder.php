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
        Permission::firstOrCreate(['name' => 'Voir Module RH', 'module' => 'RH', 'guard_name' => 'web']);

        Permission::firstOrCreate(['name' => 'Voir Employé', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ajouter Employé', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Voir Detail Employé', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier Employé', 'module' => 'RH', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Changer Gestionnaire Employé', 'module' => 'RH', 'guard_name' => 'web']);


        Permission::firstOrCreate(['name' => 'Voir Entreprise', 'module' => 'ENTREPRISE', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Ajouter Entreprise', 'module' => 'ENTREPRISE', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'Modifier Entreprise', 'module' => 'ENTREPRISE', 'guard_name' => 'web']);
    }
}
