<?php

namespace Modules\Roles\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'ADMIN']);
        Role::firstOrCreate(['name' => 'GESTIONNAIRE']);
        Role::firstOrCreate(['name' => 'EMPLOYE']);
    }
}
