<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Budget\Database\Seeders\BudgetDatabaseSeeder;
use Modules\Roles\Database\Seeders\PermissionSeeder;
use Modules\Roles\Database\Seeders\RoleSeeder;

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

        $this->command->info("--- start seed GroupSeeder ---");
        $this->call(GroupSeeder::class);

        $this->command->info("--- start seed PermissionSeeder ---");
        $this->call(PermissionSeeder::class);

        $this->command->info("--- start seed RoleSeeder ---");
        $this->call(RoleSeeder::class);

        $this->command->info("--- start seed UserSeeder ---");
        $this->call(UserSeeder::class);

        $this->command->info("--- start seed GroupUserSeeder ---");
        $this->call(GroupUserSeeder::class);

        $this->command->info("--- start seed EmployeSeeder ---");
        $this->call(EmployeSeeder::class);
    }
}
