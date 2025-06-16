<?php

namespace Modules\Roles\Database\Seeders;

use Illuminate\Database\Seeder;

class RolesDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info("--- start seed RoleSeeder ---");
        $this->call(RoleSeeder::class);
    }
}
