<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Budget\Database\Seeders\BudgetDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Log::info("--- start seed BudgetDatabaseSeeder ---");
            $this->call(BudgetDatabaseSeeder::class);
        Log::info("--- end seed BudgetDatabaseSeeder ---");

        Log::info("--- start seed GroupSeeder ---");
            $this->call(GroupSeeder::class);
        Log::info("--- start seed GroupSeeder ---");


    }
}
