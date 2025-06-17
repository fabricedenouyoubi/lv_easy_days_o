<?php

namespace Modules\Roles\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $r1 = Role::firstOrCreate(['name' => 'ADMIN']);
        $r2 = Role::firstOrCreate(['name' => 'GESTIONNAIRE']);
        $r3 = Role::firstOrCreate(['name' => 'EMPLOYE']);

        $permissions = Permission::all()->pluck('id')->toArray();

        //--- Toutes les permissoins l'administrateur
        $r1->syncPermissions($permissions);
    }
}
