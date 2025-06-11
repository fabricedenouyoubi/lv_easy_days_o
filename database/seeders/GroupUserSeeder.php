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
            ['user_id' => 3, 'group_id' => 3],
            ['user_id' => 4, 'group_id' => 3],
            ['user_id' => 6, 'group_id' => 2],
            ['user_id' => 6, 'group_id' => 1],
            ['user_id' => 6, 'group_id' => 3],
            ['user_id' => 5, 'group_id' => 1],
        ];

        DB::table('group_user')->insert($userGroups);
    }
}
