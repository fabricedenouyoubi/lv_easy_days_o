<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $userGroups = [
            ['user_id' => 1, 'group_id' => 1],
        ];

        DB::table('group_user')->insert($userGroups);
    }
}
