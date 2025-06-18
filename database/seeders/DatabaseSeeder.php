<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Budget\Database\Seeders\BudgetDatabaseSeeder;
use Modules\Roles\Database\Seeders\RoleSeeder;
use Modules\Entreprise\Database\Seeders\EntrepriseDatabaseSeeder;
use Modules\Rh\Models\Employe;
use Modules\RhFeuilleDeTempsConfig\Database\Seeders\RhFeuilleDeTempsConfigDatabaseSeeder;
use Modules\Roles\Database\Seeders\PermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {/*  */
        // User::factory(10)->create();

        $this->command->info("--- start seed BudgetDatabaseSeeder ---");
        $this->call(BudgetDatabaseSeeder::class);

        $this->command->info("--- start seed EntrepriseDatabaseSeeder ---");
        $this->call(EntrepriseDatabaseSeeder::class);

        $this->command->info("--- start seed PermissionSeeder ---");
        $this->call(PermissionSeeder::class);

        $this->command->info("--- start seed RoleSeeder ---");
        $this->call(RoleSeeder::class);

        $this->command->info("--- start seed UserSeeder ---");
        $this->call(UserSeeder::class);

        $this->command->info("--- start seed EmployeSeeder ---");
        $this->call(EmployeSeeder::class);

        $this->command->info("--- start seed RhFeuilleTempsDatabaseSeeder ---");
        $this->call(RhFeuilleDeTempsConfigDatabaseSeeder::class);
    }
}
