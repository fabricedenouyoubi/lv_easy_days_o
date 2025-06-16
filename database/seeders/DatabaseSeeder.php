<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Budget\Database\Seeders\BudgetDatabaseSeeder;
use Modules\RhEmploye\Models\Employe;

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

        $this->command->info("--- start seed UserSeeder ---");
        $this->call(UserSeeder::class);

        $this->command->info("--- start seed GroupUserSeeder ---");
        $this->call(GroupUserSeeder::class);

        $this->command->info("--- start seed EmployeSeeder ---");
        $this->call(EmployeSeeder::class);
    }
}
